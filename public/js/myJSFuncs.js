(function($) {
//    var a, b = false,
//            c = "http://google.com";
//
//    function triggerEvent(el, type) {
//        if ((el[type] || false) && typeof el[type] == 'function') {
//            el[type](el);
//        }
//    }
//
//    $(function() {
//        $('a:not([href^=#])').on('click', function(e) {
//            e.preventDefault();
//            if (confirm("Do you really want to leave now?"))
//                c = this.href;
//
//            else
//                c = "http://google.com";
//
//            triggerEvent(window, 'onbeforeunload');
//
//        });
//    });
//
//    window.onbeforeunload = function(e) {
//        if (b)
//            return;
//        a = setTimeout(function() {
//            b = true;
//            window.location.href = c;
//            c = "http://google.com";
//            console.log(c);
//        }, 500);
//        return "Do you really want to leave now?";
//    }
//    window.onunload = function() {
//        clearTimeout(a);
//    }
//    


    $.each({
        warn_tab_changes: function(event) {
            var myList = document.getElementsByClassName("form_changed");
            if (myList.length) {
                if (!confirm('Changes on the form will be lost. Continue?')){
                    event.preventDefault();
                }
                else{
                    myList[0].className="";
                    return true;
                }
            } else
            {
                return true;
            }
            return false;
        },
        CheckSomething: function() {
            return confirm('Are you sure you want to tab away without saving?');

            var loadMyTab = true;  //If I set this to false, then it always returns false.
            if (1 == 1) {
                //Show a FancyBox prompt.

                if (1 == 1) {
                    //return true;
                    loadMyTab = true;
                }
                else {
                    //return false;
                    loadMyTab = false;
                }
            }
            else {
                //return true;
                loadMyTab = true;
            }
            return loadMyTab
        },
        test: function(msg) {

        },
        myFunc: function() {
            return 'fok my';
        },
        repeatString: function(str, num) {
            out = '';
            for (var i = 0; i < num; i++) {
                out += str;
            }
            return out;
        },
        dump: function(v, howDisplay, recursionLevel) {
            howDisplay = (typeof howDisplay === 'undefined') ? "alert" : howDisplay;
            recursionLevel = (typeof recursionLevel !== 'number') ? 0 : recursionLevel;


            var vType = typeof v;
            var out = vType;

            switch (vType) {
                case "number":
                    /* there is absolutely no way in JS to distinguish 2 from 2.0
                     so 'number' is the best that you can do. The following doesn't work:
                     var er = /^[0-9]+$/;
                     if (!isNaN(v) && v % 1 === 0 && er.test(3.0))
                     out = 'int';*/
                case "boolean":
                    out += ": " + v;
                    break;
                case "string":
                    out += "(" + v.length + '): "' + v + '"';
                    break;
                case "object":
                    //check if null
                    if (v === null) {
                        out = "null";

                    }
                    //If using jQuery: if ($.isArray(v))
                    //If using IE: if (isArray(v))
                    //this should work for all browsers according to the ECMAScript standard:
                    else if (Object.prototype.toString.call(v) === '[object Array]') {
                        out = 'array(' + v.length + '): {\n';
                        for (var i = 0; i < v.length; i++) {
                            out += this.repeatString('   ', recursionLevel) + "   [" + i + "]:  " +
                                    this.dump(v[i], "none", recursionLevel + 1) + "\n";
                        }
                        out += this.repeatString('   ', recursionLevel) + "}";
                    }
                    else { //if object    
                        sContents = "{\n";
                        cnt = 0;
                        for (var member in v) {
                            //No way to know the original data type of member, since JS
                            //always converts it to a string and no other way to parse objects.
                            sContents += this.repeatString('   ', recursionLevel) + "   " + member +
                                    ":  " + this.dump(v[member], "none", recursionLevel + 1) + "\n";
                            cnt++;
                        }
                        sContents += this.repeatString('   ', recursionLevel) + "}";
                        out += "(" + cnt + "): " + sContents;
                    }
                    break;
            }

            if (howDisplay == 'body') {
                var pre = document.createElement('pre');
                pre.innerHTML = out;
                document.body.appendChild(pre)
            }
            else if (howDisplay == 'alert') {
                alert(out);
            } else if (howDisplay == 'console') {
                console.log(out);
            }

            return out;
        }
    }, $.univ._import);
})($);