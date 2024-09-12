<?php

// Include the functions.php file for utility functions like clean_input, and the product.class.php for database operations.
require_once('functions.php');
require_once('product.class.php');

// Initialize variables to hold form input values and error messages.
$code = $name = $category = $price = $availability = '';
$codeErr = $nameErr = $categoryErr = $priceErr = $availabilityErr = '';

// Create an instance of the Product class for database interaction.
$productObj = new Product();

// Check if the form was submitted using the POST method.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // Clean and assign the input values to variables using the clean_input function to prevent XSS or other malicious input.
    $code = clean_input($_POST['code']);
    $name = clean_input($_POST['name']);
    $category = clean_input($_POST['category']);
    $price = clean_input($_POST['price']);
    $availability = isset($_POST['availability']) ? clean_input($_POST['availability']) : '';

    // Validate the 'code' field: check if it's empty or if the code already exists in the database.
    if(empty($code)){
        $codeErr = 'Product Code is required';
    } else if ($productObj->codeExists($code)){
        $codeErr = 'Product Code already exists';
    }

    // Validate the 'name' field: it must not be empty.
    if(empty($name)){
        $nameErr = 'Name is required';
    }

    // Validate the 'category' field: it must not be empty.
    if(empty($category)){
        $categoryErr = 'Category is required';
    }

    // Validate the 'price' field: it must not be empty, must be a number, and greater than 0.
    if(empty($price)){
        $priceErr = 'Price is required';
    } else if (!is_numeric($price)){
        $priceErr = 'Price should be a number';
    } else if ($price < 1){
        $priceErr = 'Price must be greater than 0';
    }

    // Validate the 'availability' field: it must be selected.
    if(empty($availability)){
        $availabilityErr = 'Availability is required';
    }

    // If there are no validation errors, proceed to add the product to the database.
    if(empty($codeErr) && empty($nameErr) && empty($categoryErr) && empty($priceErr) && empty($availabilityErr)){
        // Assign the sanitized inputs to the product object.
        $productObj->code = $code;
        $productObj->name = $name;
        $productObj->category_id = $category;
        $productObj->price = $price;
        $productObj->availability = $availability;

        // Attempt to add the product to the database.
        if($productObj->add()){
            // If successful, redirect to the product listing page.
            header('Location: product.php');
        } else {
            // If an error occurs during insertion, display an error message.
            echo 'Something went wrong when adding the new product.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        /* Error message styling */
        .error{
            color: red;
        }
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
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Style for form fields */
        label {
            font-size: 16px;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Radio buttons styling */
        input[type="radio"] {
            margin-right: 10px;
        }

        label[for="instock"],
        label[for="nostock"] {
            font-weight: normal;
            font-size: 14px;
            margin-right: 20px;
        }

        /* Error messages */
        .error {
            color: red;
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* Submit button */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                padding: 15px;
            }
            
            label {
                font-size: 14px;
            }
            
            input[type="submit"] {
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
    <!-- Form to collect product details -->
    <form action="" method="post">
        <!-- Display a note indicating required fields -->
        <span class="error">* are required fields</span>
        <br>

        <!-- Product Code field with validation error display -->
        <label for="code">Code</label><span class="error">*</span>
        <br>
        <input type="text" name="code" id="code" value="<?= $code ?>">
        <br>
        <?php if(!empty($codeErr)): ?>
            <span class="error"><?= $codeErr ?></span><br>
        <?php endif; ?>

        <!-- Product Name field with validation error display -->
        <label for="name">Name</label><span class="error">*</span>
        <br>
        <input type="text" name="name" id="name" value="<?= $name ?>">
        <br>
        <?php if(!empty($nameErr)): ?>
            <span class="error"><?= $nameErr ?></span><br>
        <?php endif; ?>

        <!-- Product Category dropdown with validation error display -->
        <label for="category">Category</label><span class="error">*</span>
        <br>
        <select name="category" id="category">
            <option value="">--Select--</option>
            <?php
                $categoryList = $productObj->fetchCategory();
                foreach ($categoryList as $cat){
            ?>
                <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
            <?php
                }
            ?>
        </select>
        <br>
        <?php if(!empty($categoryErr)): ?>
            <span class="error"><?= $categoryErr ?></span><br>
        <?php endif; ?>

        <!-- Product Price field with validation error display -->
        <label for="price">Price</label><span class="error">*</span>
        <br>
        <input type="number" name="price" id="price" value="<?= $price ?>">
        <br>
        <?php if(!empty($priceErr)): ?>
            <span class="error"><?= $priceErr ?></span>
            <br>
        <?php endif; ?>

        <!-- Product Availability radio buttons with validation error display -->
        <label for="availability">Availability</label><span class="error">*</span>
        <br>
        <input type="radio" value="In Stock" name="availability" id="instock" <?= ($availability == 'In Stock') ? 'checked' : '' ?>>
        <label for="instock">In Stock</label>
        <input type="radio" value="No Stock" name="availability" id="nostock" <?= ($availability == 'No Stock') ? 'checked' : '' ?>>
        <label for="nostock">No Stock</label>
        <br>
        <?php if(!empty($availabilityErr)): ?>
            <span class="error"><?= $availabilityErr ?></span><br>
        <?php endif; ?>

        <!-- Submit button to save the product -->
        <br>
        <input type="submit" value="Save Product">
    </form>
</body>
</html>
