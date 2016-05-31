<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/Backend/css/user-content-comment.min.css">
    <link rel="stylesheet" href="/Backend/css/jquery.cssemoticons.css">
    <link rel="stylesheet" href="/Backend/css/comment_box.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <title>Comment Box</title>
</head>
<body>
<!------------container------->
{% block content %}
{% endblock %}
</body>
<input type="hidden" id="real_user_id" value="{{ user_id|default(0) }}">
<input type="hidden" id="real_user_name" value="{{ user_name|default('Anonymous') }}">
<script type="text/javascript" src="/js/jquery/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="/Backend/js/comment_box.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/Backend/js/jquery.cssemoticons.js"></script>
</html>