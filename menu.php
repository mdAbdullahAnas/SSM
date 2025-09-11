
<link rel="stylesheet" href="Asset/Css/menu.css">
<div class="main-content">
    <h1>Our Products</h1>
    <div class="product-grid">
        <?php
        $products = [
            ["name"=>"Wireless Mouse","price"=>"25","img"=>"Asset/Images/mouse.jpg"],
            ["name"=>"Smartphone","price"=>"350","img"=>"Asset/Images/smartphone.jpg"],
            ["name"=>"Headphones","price"=>"60","img"=>"Asset/Images/headphones.jpg"],
            ["name"=>"Laptop","price"=>"800","img"=>"Asset/Images/laptop.jpg"],
            ["name"=>"T-Shirt","price"=>"15","img"=>"Asset/Images/tshirt.jpg"],
            ["name"=>"Coffee Maker","price"=>"45","img"=>"Asset/Images/coffeemaker.jpg"]
        ];

        foreach($products as $p){
            echo '<div class="product-card">';
            echo '<img src="'.$p['img'].'" alt="'.$p['name'].'">';
            echo '<h3>'.$p['name'].'</h3>';
            echo '<p class="price">$'.$p['price'].'</p>';
            echo '<button class="btn">Add to Cart</button>';
            echo '</div>';
        }
        ?>
    </div>
</div>
