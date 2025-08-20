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
            <p>Congratulations and welcome to Shopeedo!</p>
            <p>We are delighted to inform you that your seller account has been successfully created. You are now a valued member of the Shopeedo community and poised to reach millions of customers and grow your business.</p>
            <h2>What’s Next?</h2>
            <ol>
                <li>
                    <strong>Complete Your Profile:</strong> Log in to your account and complete your seller profile. This includes adding your business information, uploading a profile picture, and providing any other necessary details. A complete profile helps build trust with potential customers.
                </li>
                <li>
                    <strong>List Your Products:</strong> Begin adding your products to your store. Ensure you provide high-quality images and detailed descriptions for each product. Accurate and appealing listings attract more customers and increase your chances of making sales. You can also categorize your products to make it easier for customers to find them.
                </li>
                <li>
                    <strong>Explore Seller Tools:</strong> Take advantage of the various tools and resources available on Shopeedo to optimize your listings, manage orders, and track your performance. These tools are designed to help you run your store efficiently and effectively. Explore features like inventory management, sales analytics, and promotional tools to boost your sales.
                </li>
                <li>
                    <strong>Review Policies:</strong> Familiarize yourself with Shopeedo's seller policies and guidelines to ensure compliance and smooth operations. This includes our shipping policies, return policies, and seller code of conduct. Understanding these policies will help you provide the best experience for your customers and avoid any potential issues.
                </li>
            </ol>
            <h2>Your Account Details:</h2>
            <p><strong>Username:</strong> [Your Username]<br>
            <strong>Login URL:</strong> [Login URL]</p>
            <p><strong>Need Assistance?</strong><br>Our dedicated support team is here to help you every step of the way. If you have any questions or require assistance, please reach out to us at <a href="mailto:support@shopeedo.com">support@shopeedo.com</a>. We offer various support options, including a comprehensive Help Center, live chat, and email support to address any concerns or queries you may have.</p>
            <p>We are excited to see your business thrive on Shopeedo and are committed to providing you with the best possible experience. Our community of sellers is our top priority, and we are constantly working to improve our platform to better serve your needs.</p>
            <p><strong>Stay Connected:</strong> Stay informed about the latest updates, tips, and best practices by subscribing to our seller newsletter and joining our community forums. Connect with other sellers, share your experiences, and learn from one another to enhance your selling journey on Shopeedo.</p>
            <p>Welcome aboard and happy selling!</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>The Shopeedo Team</p>
            <p><a href="[Website URL]">Visit Shopeedo</a> | <a href="mailto:support@shopeedo.com">Contact Support</a></p>
        </div>
    </div>
</body>
</html>
