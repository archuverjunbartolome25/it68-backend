from flask import Flask, jsonify
from flask_cors import CORS
import pandas as pd
import requests
from statsmodels.tsa.arima.model import ARIMA
import warnings
from statsmodels.tools.sm_exceptions import ConvergenceWarning

app = Flask(__name__)
CORS(app)

@app.route('/forecast', methods=['GET'])
def forecast():
    try:
        # Fetch historical sales data from Laravel
        response = requests.get("http://localhost:8000/api/historical-sales")
        data = response.json()

        if not data:
            return jsonify({"error": "No historical data"}), 400

        # Prepare DataFrame
        df = pd.DataFrame(data)
        df['date'] = pd.to_datetime(df['date'])
        df.set_index('date', inplace=True)

        # Aggregate to monthly totals
        df_monthly = df.resample('M').sum()

        product_columns = {
            "350ml": "qty_350ml",
            "500ml": "qty_500ml",
            "1L": "qty_1l",
            "6L": "qty_6l"
        }

        forecast_result = {}

        for product_name, column in product_columns.items():
            if column not in df_monthly.columns:
                forecast_result[product_name] = []
                continue

            ts = df_monthly[column].fillna(0).astype(float)

            if len(ts) < 2 or ts.nunique() <= 1:
                forecast_result[product_name] = [
                    {"date": str(ts.index[-1].date()), "predicted_qty": int(ts.iloc[-1]), "actual_qty": int(ts.iloc[-1])}
                ]
                continue

            with warnings.catch_warnings():
                warnings.simplefilter("ignore", ConvergenceWarning)
                model = ARIMA(ts, order=(1, 1, 1))
                model_fit = model.fit()

            # -----------------
            # 1. In-sample fitted values (backtest)
            # -----------------
            fitted_values = model_fit.fittedvalues
            fitted = [
                {
                    "date": str(date.date()),
                    "predicted_qty": int(round(val)),
                    "actual_qty": int(ts.loc[date])
                }
                for date, val in fitted_values.items()
            ]

            # -----------------
            # 2. Future forecast (12 months ahead)
            # -----------------
            forecast_steps = 12
            forecast_values = model_fit.forecast(steps=forecast_steps)
            forecast_dates = pd.date_range(
                start=ts.index[-1] + pd.offsets.MonthEnd(1),
                periods=forecast_steps,
                freq='M'
            )

            future = [
                {"date": str(date.date()), "predicted_qty": int(round(qty)), "actual_qty": None}
                for date, qty in zip(forecast_dates, forecast_values)
            ]

            # Combine past + future
            forecast_result[product_name] = fitted + future

        return jsonify({"forecast": forecast_result})

    except requests.exceptions.RequestException:
        return jsonify({"error": "Failed to fetch historical data from Laravel."}), 500
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5001)
