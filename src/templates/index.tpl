{config_load file="test.conf" section="setup"}
{include file="header.tpl" title=foo}


<literal>
<div class="navbar navbar-inverse navbar-fixed-top"">
	<div class="navbar-inner">
	<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="#">soho微信</a>
			
			<div class="nav-collapse">
				<ul class="nav">
					<li class="active"><a href="index2.php">Home</a></li>
					<li><a href="app1.php">应用1</a></li>
					<li><a href="#">应用2</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="container well well-large">
	<div id="myCarousel" class="carousel container slide">
		<ol class="carousel-indicators">
			<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			<li data-target="#myCarousel" data-slide-to="1"></li>
			<li data-target="#myCarousel" data-slide-to="2"></li>
		</ol>
		
		<!-- Carousel items -->
		<div class="carousel-inner">
			<div class="active item"><img src="bootstrap/img/slide1.jpg" alt="" /></div>
			<div class="item"><img src="bootstrap/img/slide2.jpg" alt="" /></div>
			<div class="item"><img src="bootstrap/img/slide3.jpg" alt="" /></div>
		</div>
		
		<!-- Carousel nav -->
		<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
	</div>
</div>


<div class="container well well-large">
<div class="row-fluid">
            <div class="carousel slide" id="myCarousel">
                <div class="carousel-inner">
                  <div class="item active">
                        <ul class="thumbnails">
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb1.jpg" alt="">
                                </div>
                                <p>First item<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb2.jpg" alt="">
                                </div>
                                <p>This is second<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb3.jpg" alt="">
                                </div>
                                <p>Third product<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb4.jpg" alt="">
                                </div>
                                <p>And the fourth<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                        </ul>
                  </div>
                  <div class="item">
                        <ul class="thumbnails">
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb5.jpg" alt="">
                                </div>
                                <p>Another caption<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb6.jpg" alt="">
                                </div>
                                <p>Another caption<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb7.jpg" alt="">
                                </div>
                                <p>Another caption<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                            <li class="span3">
                                <div class="thumbnail">
                                    <img src="bootstrap/img/thumb8.jpg" alt="">
                                </div>
                                <p>Another caption<br>
                                <small class="red">$19.99</small><br>
                                <a href="#" class="btn btn-success">Buy Now</a></p>
                            </li>
                        </ul>
                  </div>
                </div>
                <a data-slide="prev" href="#myCarousel" class="left carousel-control">&lsaquo;</a>
                <a data-slide="next" href="#myCarousel" class="right carousel-control">&rsaquo;</a>
            </div>
        </div>
</div>   


</literal>


<!--PRE-->
{* 从配置文件读取 *}
{if #bold#}<b>{/if}

{* 大写化第一个字母 *}
Title: {#title#|capitalize}
{if #bold#}</b>{/if}

{* 变量 读取 *}
The current date and time is {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}

The value of global assigned variable $SCRIPT_NAME is {$SCRIPT_NAME}

Example of accessing server environment variable SERVER_NAME: {$smarty.server.SERVER_NAME}

The value of {ldelim}$Name{rdelim} is <b>{$Name}</b>

variable modifier example of {ldelim}$Name|upper{rdelim}

<b>{$Name|upper}</b>


An example of a section loop:

{section name=outer 
loop=$FirstName}
{if $smarty.section.outer.index is odd by 2}
	{$smarty.section.outer.rownum} . {$FirstName[outer]} {$LastName[outer]}
{else}
	{$smarty.section.outer.rownum} * {$FirstName[outer]} {$LastName[outer]}
{/if}
{sectionelse}
	none
{/section}

An example of section looped key values:

{section name=sec1 loop=$contacts}
	phone: {$contacts[sec1].phone}<br>
	fax: {$contacts[sec1].fax}<br>
	cell: {$contacts[sec1].cell}<br>
{/section}
<p>

testing strip tags
{strip}
<table border=0>
	<tr>
		<td>
			<A HREF="{$SCRIPT_NAME}">
			<font color="red">This is a  test     </font>
			</A>
		</td>
	</tr>
</table>
{/strip}

<!--/PRE-->

This is an example of the html_select_date function:

<form>
{html_select_date start_year=1998 end_year=2010}
</form>

This is an example of the html_select_time function:

<form>
{html_select_time use_24_hours=false}
</form>

This is an example of the html_options function:

<form>
<select name=states>
{html_options values=$option_values selected=$option_selected output=$option_output}
</select>
</form>

{include file="footer.tpl"}
