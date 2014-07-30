/* Id: core.js 20.09.13 12:26 TeaJay $ */
var loginPopup = {
  formUrl:'',
  forgotUrl:'',
  signupUrl:'',
  facebookUrl:'',
  twitterUrl:'',
  dateLimit: 30,
  content:'',
  modalView:'',
  return_url: '',

  init:function(){
    var self = this;

    self.content.inject($$('body')[0]);
    self.modalView.inject($$('body')[0]);

    var lastdate = Cookie.read('en4_heloginpopup_lastdate', {path:en4.core.basePath});
    var currentdate = new Date();

    if( !lastdate ) {
      self.showPopup();
      Cookie.write('en4_heloginpopup_lastdate',currentdate, {path:en4.core.basePath});
      return;
    }

    lastdate = new Date(lastdate);

    var dateDiff = parseInt((currentdate.getTime()-lastdate.getTime())/(24*3600*1000*7));

    console.log(dateDiff);

    if( dateDiff >= self.dateLimit ) {
      self.showPopup();
      Cookie.write('en4_heloginpopup_lastdate',currentdate, {path:en4.core.basePath});
    }
  },

  showPopup:function()
  {
    this.content.setStyle('display', 'block');
    this.modalView.setStyle('display', 'block');

    this.content.addClass('heloginpopup_in');
    this.modalView.addClass('heloginpopup_in');
  },

  hidePopup:function()
  {
    var self = this;
    this.content.removeClass('heloginpopup_in');
    this.modalView.removeClass('heloginpopup_in');
    (function(){
      self.modalView.setStyle('display', 'none');
      self.content.setStyle('display', 'none');
    }).delay(200);
  }
}