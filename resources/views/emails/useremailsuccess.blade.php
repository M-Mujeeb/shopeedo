<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header, .footer {
            text-align: center;
            color: #333;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .content h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
        }
        .content p, .content li {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }
        .content ol {
            padding-left: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .footer p {
            font-size: 14px;
            color: #777;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="[Your Logo URL]" alt="Shopeedo Logo">
        </div>
        <div class="content">
            <h1>{{ $subject }}</h1>
            <p>{{ $content }}</p>
            <hr>
            <p>Congratulations and welcome to Shopeedo!</p>
            <p>We are delighted to inform you that your User account has been successfully created. You are now a valued member of the Shopeedo community and poised to reach millions of customers and grow your business.</p>
            <hr>
            <h4>A New Adventure Begins</h4>
            <h5 style="font-style: italic">YOU ARE ONE STEP AWAY FROM BUYING OR SELLING</h5>
            <p> Here’s a sneak peek of what awaits you:</p>
            <ul>
                <li>
                    <strong>Exclusive Deals:</strong> Get access to members-only promotions and discounts.
                </li>
                <li>
                    <strong>Swift Checkout:</strong> Save your details for a seamless shopping experience.
                </li>
                <li>
                    <strong>Order Updates: </strong> Stay in the loop with real-time tracking and notifications.
                </li>
                {{-- <li>
                    <strong>Review Policies:</strong> Familiarize yourself with Shopeedo's seller policies and guidelines to ensure compliance and smooth operations. This includes our shipping policies, return policies, and seller code of conduct. Understanding these policies will help you provide the best experience for your customers and avoid any potential issues.
                </li> --}}
            </ul>
            <h4>Need Assistance:</h4>
            <p>Our support team is here for you from 09:00 AM to 06:00 PM, every day of the week.
            </p>
            <h4>Contact Us</h4>
            <ul>
                <li>Phone: +971 58 5567542 </li>
                <li>Email: info@shopeedo.com</li>
            </ul>
            {{-- <p><strong>Need Assistance?</strong><br>Our dedicated support team is here to help you every step of the way. If you have any questions or require assistance, please reach out to us at <a href="mailto:support@shopeedo.com">support@shopeedo.com</a>. We offer various support options, including a comprehensive Help Center, live chat, and email support to address any concerns or queries you may have.</p>
            <p>We are excited to see your business thrive on Shopeedo and are committed to providing you with the best possible experience. Our community of sellers is our top priority, and we are constantly working to improve our platform to better serve your needs.</p>
            <p><strong>Stay Connected:</strong> Stay informed about the latest updates, tips, and best practices by subscribing to our seller newsletter and joining our community forums. Connect with other sellers, share your experiences, and learn from one another to enhance your selling journey on Shopeedo.</p>
            <p>Welcome aboard and happy selling!</p> --}}
            <p>You’re receiving this email because you (or someone else) confirmed this email address for a Shopeedo account. If you didn’t do this, please ignore this email. We value your privacy and promise never to share your information with third parties without your consent.
            </p>

            <hr>
           
        </div>
        <div class="footer">
            <p><strong>Shopeedo Headquarters:  Lahore Punjab, Pakistan.</strong></p>
            <p>Best regards,<br>The Shopeedo Team</p>
            <p><a href="https://dev.shopeedo.com">Visit Shopeedo</a> | <a href="mailto:info@shopeedo.com">Contact Support</a></p>
        </div>
    </div>
</body>
</html>
