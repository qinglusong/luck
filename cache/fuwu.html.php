<?php /* Smarty version 2.6.26, created on 2018-08-14 11:18:08
         compiled from fuwu.html */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $this->_tpl_vars['keywords']; ?>
" />
<meta name="description" content="<?php echo $this->_tpl_vars['description']; ?>
" />

<title><?php echo $this->_tpl_vars['page_title']; ?>
</title>
<link href="http://www.3e-d.com/theme/default/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://www.3e-d.com/theme/default/images/jquery.min.js"></script>
<script type="text/javascript" src="http://www.3e-d.com/theme/default/images/global.js"></script>
</head>
<body>

<?php $_from = $this->_tpl_vars['info']['contents_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<p><?php echo $this->_tpl_vars['item']; ?>
</p>
<?php endforeach; endif; unset($_from); ?>


</body>
</html>