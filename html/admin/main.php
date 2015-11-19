<body >

<table width=500>

<tr> <td>


<? 

if ($page)
{
	include($page);
}
else 
{
	include("news.php");
};

 ?> 

	
</td> </tr>

</table>


</body>
</html>
