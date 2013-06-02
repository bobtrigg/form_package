<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/global.css" rel="stylesheet" type="text/css">
<link href="../_css/form.css" rel="stylesheet" type="text/css">

<!-- jQuery to generate hidden fields for post-entry processing -->
<script src="../_js/jquery-1.6.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../_js/form_processor.js"></script>

<!-- jQuery to mark full shifts -->
<script type="text/javascript">
	$("document").ready( function () {
		$("form :checkbox.full").before('<span style="color:red;font-weight:bold;position:relative;right:10px;">FULL!</span>');
	});
</script>

<meta name="viewport" content="width=device-width,intial-scale=1, maximum-scale=1, user-scalable=no">
</head>

<body>

<form method="post" action="/_php/FormProcessor.php" name="volform" id="volform">
  <fieldset id="description" title="Description">
  <legend>Description</legend>