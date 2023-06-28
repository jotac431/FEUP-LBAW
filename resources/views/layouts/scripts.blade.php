<script src="https://kit.fontawesome.com/32dcfd1afe.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
</script>

<script src="//js.pusher.com/3.1/pusher.min.js"></script>
<script src="https://cdn.tiny.cloud/1/1b07llfopaqjsr26834ikea0i2hyvslhbteirkypr8jc45t9/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script type="text/javascript">
    // Fix for Firefox autofocus CSS bug
    // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
</script>

<script type="text/javascript" src={{ asset('js/loadMore.js') }}></script>
<script type="text/javascript" src={{ asset('js/share.js') }}></script>
<script type="text/javascript" src={{ asset('js/app.js') }}></script>
<script type="text/javascript" src={{ asset('js/notifications.js') }}></script>
<script type="text/javascript" src={{ asset('js/pusher.js') }}></script>

@yield('scripts')
