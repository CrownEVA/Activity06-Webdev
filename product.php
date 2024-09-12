<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <style>
        /* General reset and box-sizing */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        /* Center the form on the page */
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Form fields styling */
        label {
            font-size: 16px;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], 
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Submit button styling */
        input[type="submit"] {
            display: inline-block;
            background-color: #28a745;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f4f4f9;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Links for action buttons */
        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            color: #0056b3;
        }

        /* Styling for the "No product found" message */
        p.search {
            text-align: center;
            margin: 20px 0;
            color: red;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form, table {
                width: 100%;
                padding: 10px;
            }

            th, td {
                padding: 8px;
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Link to a page where users can add a new product -->
    <a href="addproduct.php">Add Product</a>

    <?php
        // Include the Product class file
        require_once 'product.class.php';

        // Create an instance of the Product class to interact with the database
        $productObj = new Product();

        // Initialize keyword and category variables for filtering
        $keyword = $category = '';
        
        // Check if the form is submitted via POST method and 'search' button is clicked
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize input from the search form
            $keyword = htmlentities($_POST['keyword']);
            $category = htmlentities($_POST['category']);
        }

        // Retrieve the filtered list of products with stock data
        $array = $productObj->showAll($keyword, $category);
    ?>

    <!-- Form for filtering products based on category and keyword -->
    <form action="" method="post">
        <label for="category">Category</label>
        <select name="category" id="category">
            <option value="">All</option>
            <?php
                $categoryList = $productObj->fetchCategory();
                foreach ($categoryList as $cat){
            ?>
                <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
            <?php
                }
            ?>
        </select>
        <label for="keyword">Search</label>
        <input type="text" name="keyword" id="keyword" value="<?= $keyword ?>">
        <input type="submit" value="Search" name="search" id="search">
    </form>

    <!-- Display the products in an HTML table -->
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Availability</th>
            <th>Total Incoming</th>
            <th>Total Outgoing</th>
            <th>Current Stock</th>
            <th>Action</th>
        </tr>
        <?php
        $i = 1;
        if (empty($array)) {
        ?>
            <tr>
                <td colspan="10"><p class="search">No product found.</p></td>
            </tr>
        <?php
        } else {
            foreach ($array as $arr) {
        ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= $arr['code'] ?></td>
            <td><?= $arr['product_name'] ?></td>
            <td><?= $arr['category_name'] ?></td>
            <td><?= $arr['price'] ?></td>
            <td><?= $arr['availability'] ?></td>
            <td><?= $arr['total_incoming'] ?></td>
            <td><?= $arr['total_outgoing'] ?></td>
            <td><?= $arr['current_stock'] ?></td>
            <td>
                <a href="editproduct.php?id=<?= $arr['id'] ?>">Edit</a>
                <a href="#" class="deleteBtn" data-id="<?= $arr['id'] ?>" data-name="<?= $arr['product_name'] ?>">Delete</a>
            </td>
        </tr>
        <?php
            $i++;
            }
        }
        ?>
    </table>
    
    <script src="./product.js"></script>
</body>
</html>
