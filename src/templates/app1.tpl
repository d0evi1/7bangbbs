{config_load file="test.conf" section="setup"}
{include file="header.tpl" title=foo}

{* һ���Ż���ť. *}
<literal>
	<script src="http://code.jquery.com/jquery.js"></script>
	<script>
		function optimize() {
			$.ajax({
			type: "POST",
			url: "keyword_optimize.php",
			success: function(msg){
				$("#index1").append(msg);
			 }
		   }); 
		}
	</script>
</literal>

<literal>
	<div class="navbar navbar-inverse navbar-fixed-top"">
		<div class="navbar-inner">
		<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				
				<a class="brand" href="#">soho΢��</a>
				
				<div class="nav-collapse">
					<ul class="nav">
						<li><a href="index2.php">Home</a></li>
						<li class="active"><a href="app1.php">Ӧ��1</a></li>
						<li><a href="#">Ӧ��2</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>


	<div class="container-fluid">
		<div class="row-fluid">	
			<div class="span24">
				<div class="hero-unit">
					<h2>����-��ש��</h2>
					<p>����С��û�ÿ�ô�����ĵ���ûת����ô����ש��һ�������㶨һ�У�</p>
					<p>
						<a class="btn btn-primary btn-large" id="taobao_login_btn">
							�Ա���½
						</a>
						<a class="btn btn-primary btn-large" id="free_btn">
							�������
						</a>
						
						
						<a class="btn btn-primary btn-large" id="1key_btn" onclick="optimize()">
							һ�������Ż�
						</a>
					</p>
				</div>
			</div>
			<div class="span24">
				<div class="hero-unit">
					<h3>����ָ��</h3>
					<span class="label label-info" id="index1">ָ��1</span>
					<span class="label label-info" id="index2">ָ��2</span>
					<p></p>
				</div>
			</div>
		</div>
	</div>	
</literal>


<!--PRE-->
{* �������ļ���ȡ *}
{if #bold#}<b>{/if}

{* ��д����һ����ĸ *}
Title: {#title#|capitalize}
{if #bold#}</b>{/if}

{* ���� ��ȡ *}
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
