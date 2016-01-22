var email = document.getElementById('subscription-val');
function add_email_list()
{
  if (check_email())
  {
	Ajax.call('user.php?act=email_list&job=add&email=' + email.value, '', rep_add_email_list, 'GET', 'TEXT');
  }
}
function rep_add_email_list(text)
{
  alert(text);
}

function check_email()
{
  if (Utils.isEmail(email.value))
  {
	return true;
  }
  else
  {
	alert('{$lang.email_invalid}');
	return false;
  }
}