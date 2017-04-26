function do_copy(el) {
    var el = document.getElementById(el);
    el.select();
    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copying text command was ' + msg);
    } catch (err) {
        console.log('Oops, unable to copy');
    }
}


function replace_with_audio(el, url) {
    var p = el.parentElement;
    a = document.createElement('audio');
    a.setAttribute('src', url);
    a.setAttribute('controls', '');
    a.setAttribute('class','form-control');
    t = document.createTextNode('Your browser does not support the <code>audio</code> element.');
    a.appendChild(t);
    p.replaceChild(a, el);
}

//<textarea class="form-control" name="transcription">
//<button type="submit" class="btn btn-primary">Wgraj transkrypcję</button>
function replace_with_transcription(el, url) {
    var p = el.parentElement;
    var t = document.createElement('textarea');
    t.setAttribute('class', 'form-control');
    t.setAttribute('name', 'transcription');

    $.get(url, function (data) {
        t.innerHTML = data;
    });

    var b = document.createElement('button');
    b.setAttribute('type', 'submit');
    b.setAttribute('class', 'btn btn-primary');
    b.innerHTML = 'Aktualizuj transkrypcję';

    p.replaceChild(t, el);
    p.appendChild(b);
}