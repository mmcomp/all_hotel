function openRoomDet(room_id)
{
	$.window({
                title: "اطلاعات اتاق",
                width: 900,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gaantinfo.php?room_id="+room_id+"&r="+Math.random()+"&"
        });
}

javascript:if(confirm('آیا مایل به خروج هستید؟')){window.location ='login.php?stat=exit&';}

