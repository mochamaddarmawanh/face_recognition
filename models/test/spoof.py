from flask import Flask, request, jsonify
from flask_cors import CORS
import sys
import os
import cv2
import numpy as np

sys.path.append(os.path.abspath(os.path.join('..', 'face_recognition')))

from assets.library.SilentFaceAntiSpoofing import test

app = Flask(__name__)
CORS(app)

@app.route('/spoofing_process', methods=['POST'])
def spoofing_process():
    data = request.files['image_data']
    nparr = np.fromstring(data.read(), np.uint8)
    image_data = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    label = test.test(image_data, r'C:\xampp\htdocs\face_recognition\assets\library\SilentFaceAntiSpoofing\resources\anti_spoof_models', 0)

    return jsonify(int(label))
    
if __name__ == '__main__':
    app.run()
