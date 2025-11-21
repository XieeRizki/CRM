<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inotal Partners - Public</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .shadow-card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .shadow-card-hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .max-h-90vh {
            max-height: 90vh;
        }
        
        /* Animasi hover */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Gradient untuk region cards */
        .bg-gradient-region {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    
    

    <main>
        @yield('content')
    </main>
</body>
</html>