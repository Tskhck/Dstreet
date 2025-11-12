<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <link rel="stylesheet" href="global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .order-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .order-container h2 {
            font-size: 28px;
            color: #222;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 16px;
            color: #222;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #32CDD5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(50, 205, 213, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .upload-section {
            border: 3px dashed #ddd;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            border-radius: 12px;
            cursor: pointer;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .upload-section:hover {
            border-color: #32CDD5;
            background: #f8fcfc;
            transform: translateY(-2px);
        }

        .upload-section i {
            font-size: 48px;
            color: #32CDD5;
            margin-bottom: 15px;
            display: block;
        }

        .upload-section p {
            color: #666;
            font-size: 16px;
            margin: 10px 0;
        }

        .preview-image {
            max-width: 250px;
            margin: 15px auto 0;
            display: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .submit-btn {
            background: #32CDD5;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: #2bb1b8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 213, 0.2);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .order-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
            width: 90%;
            max-width: 400px;
        }

        .order-popup h3 {
            font-size: 24px;
            color: #222;
            margin-bottom: 15px;
            text-align: center;
        }

        .order-popup p {
            font-size: 16px;
            color: #666;
            text-align: center;
            margin-bottom: 25px;
        }

        .order-popup.show {
            display: block;
            animation: popupFadeIn 0.3s ease;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
            display: none;
            backdrop-filter: blur(4px);
        }

        .overlay.show {
            display: block;
        }

        .popup-buttons {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 25px;
        }

        .popup-buttons button {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            flex: 1;
            transition: all 0.3s ease;
        }

        .confirm-btn {
            background: #32CDD5;
            color: white;
        }

        .cancel-btn {
            background: #ff4444;
            color: white;
        }
    </style>
</head>
<body>
    
    <div class="order-container">
        <h2>Create Your Order</h2>
        <form id="orderForm" action="submit_order.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product">Product Type</label>
                <select id="product" name="product" required>
                    <option value="">Select a product</option>
                    <option value="hoodie">Hoodie</option>
                    <option value="tshirt">T-Shirt</option>
                </select>
            </div>

            <div class="form-group">
                <label>Upload Your Design</label>
                <div class="upload-section" onclick="document.getElementById('designFile').click()">
                    <i class='bx bx-upload'></i>
                    <p>Click to upload your design</p>
                    <img id="designPreview" class="preview-image">
                    <input type="file" id="designFile" name="design" accept="image/*" style="display: none" required>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Any special instructions for your order..."></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Order</button>
        </form>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="order-popup" id="orderPopup">
        <h3>Confirm Your Order</h3>
        <p>Are you sure you want to submit this order?</p>
        <div class="popup-buttons">
            <button class="cancel-btn" onclick="hidePopup()">Cancel</button>
            <button class="confirm-btn" onclick="submitOrder()">Confirm Order</button>
        </div>
    </div>

    <script>
        // Handle file upload preview
        document.getElementById('designFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('designPreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Form submission and popup handling
        const form = document.getElementById('orderForm');
        const popup = document.getElementById('orderPopup');
        const overlay = document.getElementById('overlay');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            showPopup();
        });

        function showPopup() {
            popup.classList.add('show');
            overlay.classList.add('show');
        }

        function hidePopup() {
            popup.classList.remove('show');
            overlay.classList.remove('show');
        }

        function submitOrder() {
            form.submit();
        }
    </script>
</body>
</html>
