function copy_video_url() {
    $('#share_time_link').select();
    document.execCommand('copy');
    
    $('#share_valid').css('display', 'inline');
    $('#share_time').removeClass("btn-default");
    $('#share_time').addClass("btn-success");
}
