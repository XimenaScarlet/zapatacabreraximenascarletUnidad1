<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header('Location: /saberpepsi/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery App</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 80px;
            background: #4a3b8a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            z-index: 1000;
        }

        .sidebar-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #fff;
            font-size: 20px;
        }

        .sidebar-icon:hover,
        .sidebar-icon.active {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .sidebar-icon.active {
            background: #fff;
            color: #4a3b8a;
        }

        .main-content {
            flex: 1;
            margin-left: 80px;
            padding: 30px 40px;
            background: #f8f9fa;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-container {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 50px;
            border: none;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .date-display {
            font-size: 16px;
            font-weight: 500;
            color: #666;
        }

        .promo-banner {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .promo-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .promo-text {
            flex: 1;
        }

        .promo-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .promo-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .promo-image {
            width: 120px;
            height: 120px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="20" y="30" width="15" height="40" fill="%23fff" rx="2"/><rect x="25" y="25" width="5" height="50" fill="%23ffeb3b" rx="1"/><rect x="65" y="20" width="20" height="30" fill="%23fff" rx="10"/><circle cx="75" cy="35" r="8" fill="%23ff5722"/></svg>') center/contain no-repeat;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .sort-button {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }


        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .food-card {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .food-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background-size: cover;
            background-position: center;
        }

        .food-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .food-description {
            font-size: 12px;
            color: #999;
            margin-bottom: 15px;
        }

        .food-price {
            background: #4a3b8a;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
        }

        .restaurant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .restaurant-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .restaurant-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            margin: 20px;
            float: left;
        }

        .restaurant-info {
            padding: 20px;
            padding-left: 100px;
        }

        .restaurant-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .restaurant-type {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stars {
            color: #ffc107;
            font-size: 14px;
        }

        .rating-text {
            font-size: 12px;
            color: #666;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-left: 60px;
                padding: 20px;
            }

            .sidebar-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .promo-content {
                flex-direction: column;
                text-align: center;
            }

            .promo-image {
                margin-top: 20px;
            }

            .food-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .restaurant-info {
                padding-left: 20px;
            }

            .restaurant-image {
                float: none;
                margin: 20px auto 10px;
                display: block;
            }
        }

        .food-1 { background: linear-gradient(135deg, #fff3cd, #ffeaa7); }
        .food-2 { background: linear-gradient(135deg, #ffe8e8, #ffb3b3); }
        .food-3 { background: linear-gradient(135deg, #fff8dc, #f4e4bc); }
        .food-4 { background: linear-gradient(135deg, #e8f5e8, #c8e6c9); }

        .restaurant-1 { background: linear-gradient(135deg, #e3f2fd, #bbdefb); }
        .restaurant-2 { background: linear-gradient(135deg, #f3e5f5, #e1bee7); }
        .restaurant-3 { background: linear-gradient(135deg, #fff3e0, #ffcc80); }
        .restaurant-4 { background: linear-gradient(135deg, #e8f5e8, #c8e6c9); }
    </style>
</head>
<body>
    <div class="container">

        <div class="sidebar">
            <a href="error.php" class="sidebar-icon" title="Inicio"><i class="fas fa-home"></i></a>
            <a href="error.php" class="sidebar-icon" title="Buscar"><i class="fas fa-search"></i></a>
            <a href="error.php" class="sidebar-icon" title="Categorías"><i class="fas fa-th-large"></i></a>
            <a href="error.php" class="sidebar-icon" title="Carrito"><i class="fas fa-shopping-cart"></i></a>
            <a href="error.php" class="sidebar-icon" title="Perfil"><i class="fas fa-user"></i></a>
            <a href="error.php" class="sidebar-icon" title="Configuración"><i class="fas fa-cog"></i></a>
            <form method="post" action="logout.php" style="margin-top:auto;">
                <button type="submit" class="sidebar-icon" title="Cerrar sesión" style="border:none;background:none;cursor:pointer;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
        <div class="main-content">
            <div class="header" style="justify-content:center;">
                <div class="search-container" style="margin:0 auto;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar producto...">
                </div>
            </div>
            <div class="flex items-center gap-4" style="justify-content:center; margin-bottom:20px;">
                <div class="date-display"></div>
                <a href="error.php" class="text-gray-700 hover:text-pepsiBlue transition-colors">
                    <?php echo htmlspecialchars($_SESSION['empleado_nombre']); ?>
                </a>
            </div>
            <div class="food-grid">
                <div class="food-card food-1">
                    <div class="food-image" style="background-image:url('/saberpepsi/img/pepsi-original.png');background-color:#fff;"></div>
                    <div class="food-name flex flex-col items-center">
                        <span>Pepsi Original</span>
                        <span class="text-xs font-semibold text-blue-700 bg-blue-100 rounded px-2 py-0.5 mt-1">Clásico</span>
                    </div>
                    <div class="flex items-center justify-center my-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="ml-2 text-sm font-semibold">5.0</span>
                    </div>
                    <div class="food-description">El sabor original que ha refrescado generaciones. Una experiencia única en cada sorbo.</div>
                    <div class="food-price mt-3 w-full">$18.00 MXN</div>
                </div>
                <div class="food-card food-2">
                    <div class="food-image" style="background-image:url('/saberpepsi/img/pepsi-zero.png');background-color:#fff;"></div>
                    <div class="food-name flex flex-col items-center">
                        <span>Pepsi Zero Sugar</span>
                        <span class="text-xs font-semibold text-purple-700 bg-purple-100 rounded px-2 py-0.5 mt-1">Zero Azúcar</span>
                    </div>
                    <div class="flex items-center justify-center my-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-gray-300"></i>
                        <span class="ml-2 text-sm font-semibold">4.0</span>
                    </div>
                    <div class="food-description">Todo el sabor Pepsi que amas, sin azúcar. Perfecto para un estilo de vida saludable.</div>
                    <div class="food-price mt-3 w-full">$19.00 MXN</div>
                </div>
                <div class="food-card food-3">
                    <div class="food-image" style="background-image:url('/saberpepsi/img/pepsi-zero-cafeina.png');background-color:#fff;"></div>
                    <div class="food-name flex flex-col items-center">
                        <span>Pepsi Zero Cafeína</span>
                        <span class="text-xs font-semibold text-green-700 bg-green-100 rounded px-2 py-0.5 mt-1">Sin Cafeína</span>
                    </div>
                    <div class="flex items-center justify-center my-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                        <span class="ml-2 text-sm font-semibold">4.5</span>
                    </div>
                    <div class="food-description">El refrescante sabor Pepsi sin cafeína. Perfecto para cualquier momento del día.</div>
                    <div class="food-price mt-3 w-full">$19.50 MXN</div>
                </div>
                <div class="food-card food-4">
                    <div class="food-image" style="background-image:url('/saberpepsi/img/pepsi-cherry.png');background-color:#fff;"></div>
                    <div class="food-name flex flex-col items-center">
                        <span>Pepsi Cherry</span>
                        <span class="text-xs font-semibold text-pink-700 bg-pink-100 rounded px-2 py-0.5 mt-1">Sabor Especial</span>
                    </div>
                    <div class="flex items-center justify-center my-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="ml-2 text-sm font-semibold">5.0</span>
                    </div>
                    <div class="food-description">Una deliciosa combinación de Pepsi con sabor a cereza. Una explosión de sabor.</div>
                    <div class="food-price mt-3 w-full">$20.00 MXN</div>
                </div>
                <div class="food-card food-1">
                    <div class="food-image" style="background-image:url('/saberpepsi/img/pepsi-lime.png');background-color:#fff;"></div>
                    <div class="food-name flex flex-col items-center">
                        <span>Pepsi Lime</span>
                        <span class="text-xs font-semibold text-lime-700 bg-lime-100 rounded px-2 py-0.5 mt-1">Edición Limitada</span>
                    </div>
                    <div class="flex items-center justify-center my-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-gray-300"></i>
                        <span class="ml-2 text-sm font-semibold">4.0</span>
                    </div>
                    <div class="food-description">El refrescante sabor de Pepsi con un toque de lima. Perfecto para el verano.</div>
                    <div class="food-price mt-3 w-full">$21.00 MXN</div>
                </div>
                <div class="food-card food-2">
                    <div class="food-image" style="background-image:url('/saberpepsi/img/pepsi-mango.png');background-color:#fff;"></div>
                    <div class="food-name flex flex-col items-center">
                        <span>Pepsi Mango</span>
                        <span class="text-xs font-semibold text-orange-700 bg-orange-100 rounded px-2 py-0.5 mt-1">Nuevo Sabor</span>
                    </div>
                    <div class="flex items-center justify-center my-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                        <span class="ml-2 text-sm font-semibold">4.5</span>
                    </div>
                    <div class="food-description">Una deliciosa fusión de Pepsi con sabor a mango. Una experiencia tropical.</div>
                    <div class="food-price mt-3 w-full">$21.50 MXN</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.food-card').forEach(card => {
                const name = card.querySelector('.food-name span').textContent.toLowerCase();
                const desc = card.querySelector('.food-description').textContent.toLowerCase();
                if (name.includes(searchTerm) || desc.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
