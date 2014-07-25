// JS with localized strings.
function _t(id)
{
  var _t = <?php echo json_encode($_t, true) ?>;
  if (typeof(_t[id]) !== 'undefined') return _t[id];
  return id;
}
