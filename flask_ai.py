from flask import Flask, request, jsonify
import numpy as np
from sklearn.ensemble import RandomForestClassifier
import random

app = Flask(__name__)

# Mock trained model (replace with actual XGBoost/RandomForest)
model = RandomForestClassifier(n_estimators=10)
# Dummy training
model.fit([[5,5,5,5,5,5], [1,1,1,1,1,1]], ['CRITICAL', 'LOW'])

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json
    features = [data.get('food_security',5), data.get('flood_risk',5), 
                data.get('disease_risk',5), data.get('conflict',5), 
                data.get('water_san',5), data.get('nutrition',5)]
    # Dummy prediction with weighted logic for demo
    score = sum(features) / 6
    if score > 7: risk = 'CRITICAL'
    elif score > 5: risk = 'HIGH'
    elif score > 3: risk = 'MEDIUM'
    else: risk = 'LOW'
    
    return jsonify({
        'risk_level': risk,
        'vulnerability_index': round(score * 10, 2),
        'probabilities': {'LOW':0.1, 'MEDIUM':0.2, 'HIGH':0.3, 'CRITICAL':0.4}
    })

@app.route('/optimize', methods=['POST'])
def optimize():
    # Mock VRP
    return jsonify({
        'route_order': ['Monguno', 'Gubio', 'Dikwa'],
        'total_km': 120.5,
        'trucks_required': 3,
        'plan_id': random.randint(100, 999)
    })

@app.route('/retrain', methods=['POST'])
def retrain():
    print("AI Model Retrained (Mock)")
    return jsonify({'status': 'success'})

if __name__ == '__main__':
    app.run(debug=True, port=5000)