$(document).ready(function () {
	$(window).load(function() {
		var text = document.getElementById('magnifying-text');
		if($(".dragdealer").length) {
			new Dragdealer('magnifier',
			{
				steps: 500,
				snap: true,
				animationCallback: function(x, y)
				{
					if(x<=0.5) {
						$("#switchFrame").hide();
						$("#eventCalender").show();
					} else {
						$("#eventCalender").hide();
						$("#switchFrame").show();
					}
				}
			});
		}
	});
});