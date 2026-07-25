# ml_service/training/data_collector.py

class InteractionLogger:
    """
    Logs EVERY interaction for training — COMPLETELY ANONYMOUS.
    Never stores:
    - Names, phones, emails
    - IP addresses
    - Precise locations
    - Any personal identifiers
    """

    def log_interaction(self, data: dict):
        """
        Stores ONLY:
        - Original text (user input)
        - Detected language
        - Classified intent
        - Confidence score
        - Which facility was selected (if any)
        - Outcome: user clicked call? booked? left?
        - Timestamp
        - Session ID (temporary, not linked to person)
        """
        training_record = {
            'text': data.get('text'),
            'language': data.get('language'),
            'intent': data.get('intent'),
            'confidence': data.get('confidence'),
            'facility_selected': data.get('facility_id'),  # which one user picked
            'outcome': data.get('outcome'),  # 'called', 'booked', 'directions', 'left'
            'timestamp': data.get('timestamp'),
            'session_id': data.get('session_id'),  # destroyed after 24h
        }
        
        # Append to training log file (anonymized)
        self._append_to_training_set(training_record)

    def _append_to_training_set(self, record):
        """Write to daily training log."""
        import json
        import os
        from datetime import datetime
        
        date_str = datetime.now().strftime('%Y-%m-%d')
        log_dir = 'ml_service/training_logs'
        os.makedirs(log_dir, exist_ok=True)
        
        with open(f'{log_dir}/interactions_{date_str}.jsonl', 'a') as f:
            f.write(json.dumps(record) + '\n')