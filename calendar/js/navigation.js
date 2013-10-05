/**
 * Calendar - sidebox navigation
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker@stylite.de>
 * @package calendar
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * Sidebox navigation for calendar
 * 
 * @todo add code from jscalendar->flat(), or better replace it altogether ...
 */
(function()
{
	var script_tag = document.getElementById('calendar-navigation-script');
	var current_view_url;
	if (script_tag)
	{
		current_view_url = script_tag.getAttribute('data-current-view-url');
	}
	function load_cal(url,id,no_reset) {
		var owner='';
		var i = 0;
		selectBox = document.getElementById(id);
		for(i=0; i < selectBox.length; ++i) {
			if (selectBox.options[i].selected) {
				owner += (owner ? ',' : '') + selectBox.options[i].value;
			}
		}
		if (owner) {
			if (typeof no_reset == 'unknown') no_reset = false;
			egw_appWindow('calendar').location=url+'&owner='+(no_reset?'':'0,')+owner;
		}
	}
	
	/**
	 * Initialisation after DOM *and* jQuery is loaded
	 */
	egw_LAB.wait(function() {
		$j(function(){
			var calendar_window = egw_appWindow('calendar'); 
			// change handlers setting a certain url, eg. view
			$j('#calendar_view').change(function(){
				calendar_window.location = egw_webserverUrl+'/index.php?'+this.value;
			});
			// calendar owner selection change
			$j('#uical_select_owner,#uical_select_resource').change(function(e){
				if (this.value != 'popup')
				{
					load_cal(current_view_url, this.id, this.id != 'uical_select_owner');
					e.preventDefault();
				}
			});
			// diverse change handlers appending a name=value to url
			$j('#calendar_merge,#calendar_filter,#calendar_cat_id').change(function(){
				var val = $j(this).val();
				if ($j.isArray(val)) val = val.join(',');
				calendar_window.location = current_view_url+
					(current_view_url.search.length ? '&' : '?')+this.name+'='+val;
				if (this.name == 'merge') this.value='';	
			});
			// click handler to switch selectbox to multiple
			$j('#calendar_cat_id_multiple').click(function(){
				var selectBox = document.getElementById(this.id.replace('_multiple', ''));
				if (selectBox && !selectBox.multiple) 
				{
					selectBox.size=4; 
					selectBox.multiple=true;
				}
			});
		});
	});
})();