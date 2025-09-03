<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta
			http-equiv="X-UA-Compatible"
			content="IE=edge" />
		<meta
			name="viewport"
			content="width=device-width, initial-scale=1.0" />
		<meta
			name="description"
			content="All blood tests for hormones, vitamin and general health on the isle of wight." />

			  <?php if (isset($_COOKIE['branch'])) {
                        $branch = $_COOKIE['branch'];
                        if( $branch =="iow"){
                        echo "<title>NL CLINIC Isle Of Wight -All blood tests for hormones</title>";
                        }else if($branch =="sa"){
                         echo "<title>NL CLINIC Southampton -All blood tests for hormones</title>";
                        }else if($branch =="harlow"){
                                                   echo "<title>NL CLINIC Harlow</title>";
                                                  }
                    }else{
                    echo "<title>NL CLINIC -All blood tests for hormones</title>";
                    }?>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/favicon_io/site.webmanifest">

		<link
			rel="preconnect"
			href="https://fonts.googleapis.com" />
		<link
			rel="preconnect"
			href="https://fonts.gstatic.com"
			crossorigin />
		<link
			href="https://fonts.googleapis.com/css2?family=Catamaran&family=Inter:opsz,wght@14..32,100..900&family=Plus+Jakarta+Sans:wght@200..800&family=Poppins:wght@500&family=Urbanist:wght@100..900&display=swap"
			rel="stylesheet" />

		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
			rel="stylesheet"
			integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
			crossorigin="anonymous" />
		<link
			rel="stylesheet"
			href="/assets/css/style.min.css" />
		<link
			rel="stylesheet"
			href="/assets/css/pages/category.min.css" />

		<script
			src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
			crossorigin="anonymous"></script>
		<script
			src="/assets/js/index.min.js"
			defer></script>
	</head>

