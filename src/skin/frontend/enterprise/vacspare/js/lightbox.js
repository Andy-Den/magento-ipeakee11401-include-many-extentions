     var Lightbox = Class.create({	
            initialize : function(containerDiv) {
                        this.container = containerDiv;
                        this._hideLayer(this.container);
                },
                _hideLayer : function hideLayer(userAction){
                        $(userAction).style.display="none";
                },
                _makeVisible : function makeVisible(){
                        if($('bg_fade') == null) {
                                shade = new Element('div', {'id': 'bg_fade',
                                                    'style': 'visibility=hidden;'});
                                document.body.appendChild(shade);
                        }
                        $("bg_fade").setOpacity(0);
                        $("bg_fade").style.visibility="visible";
                        $("bg_fade").style.height='101%';
                },
                _makeInvisible : function makeInvisible(){
                        $("bg_fade").setOpacity(0);
                        $("bg_fade").style.visibility="hidden";
                        $("bg_fade").style.height='2px';
                },
                _showLayer : function showLayer(userAction){
                        $(userAction).style.display="block";
                },
                _hideLayer : function hideLayer(userAction){
                        $(userAction).style.display="none";
                },
                _fade : function fadeBg(userAction,whichDiv){
                        if(userAction=='close'){
                                new Effect.Opacity('bg_fade',
                                                {duration:.5,
                                                    from:0.7,
                                                    to:0,
                                                    afterFinish:this._makeInvisible(),
                                                    afterUpdate:this._hideLayer(whichDiv)});
                        }else{
                                new Effect.Opacity('bg_fade',
                                                {duration:.5,
                                                    from:0,
                                                    to:0.7,
                                                    beforeUpdate:this._makeVisible(),
                                                    afterFinish:this._showLayer(whichDiv)});
                        }
                },
                createPostion: function(){
                   var left = (screen.width/2)-(490/2);
                   var top = (document.viewport.getHeight()/2)-(475/2); 
                   $(this.container).setStyle({'display':'block','top':top+'px', 'left':left+'px'});
                },
                open : function () {
                        this.createPostion();
                        this._fade('open', this.container);
                },
                close : function () {
                        this._fade('close', this.container);
                }
        });  