
function showtab(id){

    var names = new Array ("tabname_1","tabname_2");
    var conts = new Array ("tabcontent_1","tabcontent_2");

    for(var i=0;i<names.length;i++) {
        document.getElementById(names[i]).className = 'nonactive';
    }

    for(i=0;i<conts.length;i++) {
        document.getElementById(conts[i]).className = 'hide';
    }
    
    document.getElementById('tabname_' + id).className = 'active';
    document.getElementById('tabcontent_' + id).className = 'show';
}


function showsubtab(id){

    for(var i=1;i<=8;i++) {
        if(document.getElementById("subtabcontent_" + i))
        {
            document.getElementById("subtabcontent_" + i).className = 'hide';
        }
    }

    document.getElementById('subtabcontent_' + id).className = 'show';
}