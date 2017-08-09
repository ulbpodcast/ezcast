function copy_video_url() {
    $('#share_time_link').select();
    document.execCommand('copy');
    $('#share_valid').css('display', 'inline');
    $('#share_time').css('background-color', '#2ebb2e');
}
