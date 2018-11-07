function	ft_getId(id)
{
    return(document.getElementById(id));
}

function ft_delalert(id)
{
	ft_getId(id).innerText = "";
	ft_getId(id).parentNode.className = "";
}

function	ft_new_ajax()
{
    if (window.XMLHttpRequest)
        var oAjax = new XMLHttpRequest();
    else {
        var oAjax = new ActiveXObject("Microsoft.XMLHTTP");
    }
    return (oAjax);
}

function	ft_ajax(url, data, ft_succes, ft_faild)
{
    var oAjax = ft_new_ajax();
    if (!oAjax)
        return (0);
    oAjax.open('post', url, true);
    oAjax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oAjax.onreadystatechange = function()
    {
        if (oAjax.readyState == 4)
        {
            if (oAjax.status == 200)
            {
                ft_succes(oAjax.responseText);
            }
            else if (ft_faild)
            {
                ft_faild(oAjax.status);
            }
        }
    }
    oAjax.send(data);
}

function    ft_redirect(url)
{
    window.location.replace(url);
}
