<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>错误页面</title>
  <link href="__ASSETS__/css/bootstrap.min.css" rel="stylesheet">
  <link href="{$__ADMIN__}/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="{$__ADMIN__}/css/style.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fff;
    }

    .error-page {
      height: 100%;
      position: fixed;
      width: 100%;
    }

    .error-body {
      padding-top: 5%;
    }

    .error-body h1 {
      font-size: 210px;
      font-weight: 700;
      text-shadow: 4px 4px 0 #f5f6fa, 6px 6px 0 #33cabb;
      line-height: 210px;
      color: #33cabb;
    }

    .error-body h4 {
      margin: 30px 0px;
    }
  </style>
</head>

<body>
  <section class="error-page">
    <div class="error-box">
      <div class="error-body text-center">
        <h1>404</h1>
        <h4>很抱歉，但是那个页面看起来已经不存在了。{$admin.page_title}__MODULE__ __ASSETS__</h4>
        <a href="#" onclick="history.go(-1);" class="btn btn-primary ">返回</a>
      </div>
    </div>
    
  </section>
</body>

</html>