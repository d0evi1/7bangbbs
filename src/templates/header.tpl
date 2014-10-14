<!DOCTYPE html>
<HTML>
<HEAD>
	<TITLE>{$title} - {$Name}</TITLE>
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
	
	<script type="text/javascript">
      $(document).ready(function() {
        $('.carousel').carousel();
      });
    </script>
	
	<style>
      body { background: url(assets/img/bglight.png); margin: 50px 0; }
      .well { background-color: #12fff; }
      .carousel {
        height: 400px;
        overflow: hidden;
      }
      .carousel .item {
        -webkit-transition: opacity 1s;
        -moz-transition: opacity 1s;
        -ms-transition: opacity 1s;
        -o-transition: opacity 1s;
        transition: opacity 1s;
      }
      .carousel .active.left, .carousel .active.right {
        left:0;
        opacity:0;
        z-index:2;
      }
      .carousel .next, .carousel .prev {
        left:0;
        opacity:1;
        z-index:1;
      }
    </style>
	
	
	<style>
      body { background: url(assets/img/bglight.png); margin: 50px 0; }
      .well { background-color: #fff; }
      .thumbnail {
        width: 260px;
        height: 180px;
        overflow: hidden;
        border: 0;
        box-shadow: 0 12px 12px -10px #c4c4c4;
        -webkit-box-shadow: 0 17px 22px -20px #c4c4c4;
        -moz-box-shadow: 0 12px 12px -10px #c4c4c4;
      }
      .thumbnail img { width:100%; height:auto; }
      .thumbnails p {
        text-align: center;
        padding: 10px;
      }
    </style>
</HEAD>
<BODY>
	
