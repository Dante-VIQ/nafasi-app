# ml_service/app.py

from flask import Flask, request, jsonify
from language_detector import LanguageDetector
from intent_classifier import IntentClassifier
import time
from training.daily_trainer import DailyTrainer

from context.african_context_engine import AfricanContextEngine
from learning.continuous_learner import ContinuousLearner

app = Flask(__name__)

# Initialize services (loaded once at startup)
language_detector = LanguageDetector()
intent_classifier = IntentClassifier()
context_engine = AfricanContextEngine()
learner = ContinuousLearner()

@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint."""
    return jsonify({
        'status': 'ok',
        'service': 'nafasi-ml',
        'version': '1.0.0',
        'timestamp': time.time(),
    })

@app.route('/classify', methods=['POST'])
def classify():
    """
    Main classification endpoint.
    Accepts text, returns language + intent.
    """
    try:
        data = request.get_json()
        
        if not data or 'text' not in data:
            return jsonify({'error': 'Missing "text" field'}), 400
        
        text = data['text'].strip()
        
        if len(text) < 2:
            return jsonify({'error': 'Text too short'}), 400
        
        # Detect language
        lang_result = language_detector.detect(text)
        
        # Classify intent
        intent_result = intent_classifier.classify(text, lang_result['language'])
        
        return jsonify({
            'text': text,
            'language': lang_result['language'],
            'language_confidence': lang_result['confidence'],
            'is_crisis': intent_result['is_crisis'],
            'is_emergency': intent_result['is_emergency'],
            'emergency_type': intent_result['emergency_type'],
            'facility_hints': intent_result['facility_hints'],
            'needs_dispatch': intent_result['needs_dispatch'],
            'is_anonymous_report': intent_result['is_anonymous_report'],
            'confidence': intent_result['confidence'],
            'matched_signals': intent_result['matched_signals'],
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/detect-language', methods=['POST'])
def detect_language():
    """Language detection only."""
    data = request.get_json()
    if not data or 'text' not in data:
        return jsonify({'error': 'Missing "text" field'}), 400
    
    result = language_detector.detect(data['text'])
    return jsonify(result)

@app.route('/train', methods=['POST'])
def train():
    """Trigger model training."""
    try:
        trainer = DailyTrainer()
        result = trainer.train()
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/context/transport', methods=['POST'])
def get_transport_plan():
    """Get transport plan based on African realities."""
    data = request.get_json()
    plan = context_engine.get_transport_plan(
        data.get('location_type', 'rural'),
        data.get('urgency', 'normal'),
        data.get('time_of_day', '12:00'),
    )
    return jsonify(plan)

@app.route('/context/seasonal-risks', methods=['GET'])
def get_seasonal_risks():
    """Get current seasonal health risks."""
    risks = context_engine.get_seasonal_risk_factors()
    return jsonify(risks)

@app.route('/context/economic', methods=['POST'])
def get_economic_context():
    """Get economic context for health decisions."""
    data = request.get_json() or {}
    context = context_engine.get_economic_context(data)
    return jsonify(context)

@app.route('/context/health-decision', methods=['POST'])
def get_health_decision_context():
    """Understand how health decisions are made in this context."""
    data = request.get_json() or {}
    context = context_engine.understand_health_decision_context(data)
    return jsonify(context)

@app.route('/learn', methods=['POST'])
def learn():
    """Learn from recent interactions."""
    data = request.get_json() or {}
    days = data.get('days', 7)
    result = learner.learn_from_interactions(days)
    return jsonify(result)

@app.route('/predict/demand', methods=['GET'])
def predict_demand():
    """Predict demand based on learned patterns."""
    prediction = learner.predict_demand()
    return jsonify(prediction)

@app.route('/insights', methods=['GET'])
def get_insights():
    """Get learned insights."""
    return jsonify(learner.learned_patterns)
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)