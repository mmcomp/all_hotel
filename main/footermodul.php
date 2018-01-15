<script>
	jQuery(document).ready(function() {
        
		$("#sidebar-collapse").children("i").click();
		$(".calendar").css("position","fixed");
//     var f = document.getElementById('foo');
//     setInterval(function() {
// 			console.log(f.style.color);
//         f.style.color = (f.style.color==='red')?'rgb(255, 184, 72)':'red';
//     }, 1000);

	});
    $(window).bind("load", function() {
        $(".isotope").resize();
});
</script>