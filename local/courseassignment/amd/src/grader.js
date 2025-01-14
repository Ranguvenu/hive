/**
 * local courses
 *
 * @package    local_courseassignment
 * @copyright  2019 eAbyas <eAbyas.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 define(['jquery',
 'core/str',
 'core/modal_factory',
 'core/modal_events',
 'core/fragment',
 'core/ajax',
 'core/yui',
 'local_courseassignment/dataTables',
 'local_costcenter/cardPaginate',
 'jqueryui'], function ($, Str, ModalFactory, ModalEvents, Fragment, Ajax, Y , DataTable,CardPaginate) {


    /**
    * Constructor
    *
    * @param {object} args
    *
    * Each call to init gets it's own instance of this class.
    */
    var grader = function (args) {

    this.contextid = args.contextid;
    this.method = args.method;
    var reason = this.method+'reason';
    this.reason = args.reason;
    this.args = args;
    this.init(args);
    };

    /**
     * @var {Modal} modal
     * @private
     */
    grader.prototype.modal = null;

    /**
     * @var {int} contextid
     * @private
     */
    grader.prototype.contextid = -1;
    /**
         * Initialise the class.
         *
         * @param {String} selector used to find triggers for the new group modal.
         * @private
         * @return {Promise}
         */
    grader.prototype.init = function(args) {
        // Fetch the title string.
        var self = this;
        if (args.method == 'reject') {
            var head = Str.get_string('reject_grade', 'local_courseassignment');
        } else if (args.method == 'reset') {
            var head = Str.get_string('reset_grade', 'local_courseassignment');
        }
        return head.then(function(title) {
            // Create the modal.
            return ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: title,
                body: this.getBody(args),
                footer: this.getFooter(),
            });
        }.bind(this)).then(function(modal) {
            // Keep a reference to the modal.
            this.modal = modal;
            this.modal.setLarge();

            // We want to reset the form every time it is opened.
            this.modal.getRoot().on(ModalEvents.hidden, function() {
                this.modal.getRoot().animate({"right":"-85%"}, 500);
                setTimeout(function(){
                    modal.destroy();
                }, 1000);
                this.modal.setBody('');
            }.bind(this));
            this.modal.getFooter().find('[data-action="save"]').on('click', this.submitForm.bind(this));
            // We also catch the form submit event and use it to submit the form with ajax.

            this.modal.getFooter().find('[data-action="cancel"]').on('click', function() {
                modal.setBody('');
                modal.hide();
                setTimeout(function(){
                    modal.destroy();
                }, 1000);
              /*   if (args.form_status !== 0 ) {
                    window.location.reload();
                } */
            });
            this.modal.getRoot().on('submit', 'form', function(form) {
                self.submitFormAjax(form, this.args);
            });
            this.modal.show();
            this.modal.getRoot().animate({"right":"0%"}, 500);
            return this.modal;
        }.bind(this));
    };

    /**
     * @method getBody
     * @private
     * @return {Promise}
     */
    grader.prototype.getBody = function(args) {
        if (typeof formdata === "undefined") {
            formdata = {};
        }
     
        // Get the content of the modal.
        var params = {};
        params.contextid = args.contextid;
        params.userid = args.userid;
        params.moduleid = args.moduleid;
        params.courseid = args.courseid;
        params.method = args.method;
        params.options = JSON.stringify(args.options);
        params.dataoptions = JSON.stringify(args.dataoptions);
        params.filterdata = JSON.stringify(args.filterdata);
        
        return Fragment.loadFragment('local_courseassignment', 'grade_action', this.contextid, params);
    };
    /**
     * @method getFooter
     * @private
     * @return {Promise}
     */
    grader.prototype.getFooter = function() {

        $footer = '<button type="button" class="btn btn-primary" data-action="save">Save & Continue</button>&nbsp;';
        $footer += '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>';
        return $footer;
    };
    /**
     * @method getFooter
     * @private
     * @return {Promise}
     */
    grader.prototype.getcontentFooter = function() {
        $footer = '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>';
        return $footer;
    };
    /**
     * @method handleFormSubmissionResponse
     * @private
     * @return {Promise}
     */
    grader.prototype.handleFormSubmissionResponse = function(args) {
        
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    grader.prototype.handleFormSubmissionFailure = function(data) {
        this.modal.setBody(this.getBody(data));
    };

    /**
     * Private method
     *
     * @method submitFormAjax
     * @private
     * @param {Event} e Form submission event.
     */
    grader.prototype.submitFormAjax = function(e, args) {
        e.preventDefault();
        var self = this;
       // Convert all the form elements values to a serialised string.
        var formData = this.modal.getRoot().find('form').serialize();
        var params = {};
        params.contextid = this.contextid;
        params.jsonformdata = JSON.stringify(formData);
       
        var jsondata = $('form').serializeArray();
        var dataObj = {};
        var method;var reason;
        $.each(jsondata,function(index,key){
            if(key.name == 'method'){
                method = key.value;
                reason = method+'reason';
            }
            if(key.name == reason){ 
                if(key.value.trim()=='')
                {
                    $(".specifyreason").attr("style", "display:block;color:red");
                    return false;
                }   
            } 
            if(key.name == 'options'){
                params.options  = JSON.stringify(key.value);
                dataObj.options = key.value;
            }
            if(key.name == 'dataoptions'){
                params.dataoptions = JSON.stringify(key.value);
                dataObj.dataoptions = key.value;
            } 
            if(key.name == 'filterdata'){
                params.filterdata = JSON.stringify(key.value);
                dataObj.filterdata = key.value;
            }
        if(key.name == 'method'){
            method == key.value;
        }
         });         
       
        var promise = Ajax.call([{
            methodname: 'local_courseassignment_submit_graderaction',
            args: params
        }]);
        promise[0].done(function(resp){
            self.modal.hide();
            self.modal.destroy();          
            /* if(method == 'reset'){
                element = '<td style="width:155px;word-wrap:break-word;" class="cell c5" id="mod_assign_grading_r1_c5"><a href="javascript:void(0)" onclick="(function(e){ require(\'local_courseassignment/grader\').acceptGrade({contextid:1, userid:4068 , moduleid:990, courseid:436, method : \'approve\'}) })(event)" id="yui_3_17_2_1_1676464259772_1503"><i class="fa fa-check-circle" title="Approve"></i></a><a href="javascript:void(0)" onclick="(function(e){ require(\'local_courseassignment/grader\').init({contextid:1, userid:4068 , moduleid:990, courseid:436, method : \'reject\'}) })(event)"><i class="fa fa-close" title="Reject"></i> </a></td>';
            }else{
                element = '<td>hello</td>';
            }
            $(self.args.event.target).parents('td').replace(element); */
            CardPaginate.reload( JSON.parse(dataObj.options), JSON.parse(dataObj.dataoptions),JSON.parse(dataObj.filterdata) );
        }).fail(function(){
            //self.handleFormSubmissionFailure(formData);
        });

    };

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    grader.prototype.submitForm = function(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    };

    return {
        init: function (args) {
 
            return new grader(args);
        },
        acceptGrade: function (args) {
            return Str.get_strings([{
              key: 'confirm'
            },
            {
              key: 'confirm_grade',
              component: 'local_courseassignment',
              param: args
            }]).then(function (s) {
              ModalFactory.create({
                title: s[0],
                type: ModalFactory.types.DEFAULT,
                body: s[1],
                footer: '<button type="button" class="btn btn-primary" data-action="save">Yes</button>&nbsp;' +
                  '<button type="button" class="btn btn-secondary" data-action="cancel">No</button>'
              }).done(function (modal) {
                this.modal = modal;
                modal.getRoot().find('[data-action="save"]').on('click', function () {
                   var params = {};
                   params.contextid = args.contextid;
                   params.userid = args.userid;
                   params.moduleid = args.moduleid;
                   params.courseid = args.courseid;
                   params.method = args.method;//
                   params.confirm = true;
                   params.options = JSON.stringify(args.options);
                   params.dataoptions = JSON.stringify(args.dataoptions);                  
                   
                  var promise = Ajax.call([{
                    methodname: 'local_courseassignment_approve_graderaction',
                    args: params
                  }]);
                  promise[0].done(function () {
                    modal.setBody('');
                    modal.hide();
                    CardPaginate.reload(args.options, args.dataoptions,args.filterdata);
                    //window.location.href = window.location.href;
                  }).fail(function (ex) {
                    // do something with the exception
                    console.log(ex);
                  });
                }.bind(this));
                modal.getFooter().find('[data-action="cancel"]').on('click', function () {
                  modal.setBody('');
                  modal.hide();
                });
                modal.show();
              }.bind(this));
            }.bind(this));
        },
        load: function () {
            Str.get_strings([{
                key: 'search',
            }]).then(function (str) {
                $('#tab').dataTable({
                    "autoWidth": true,
                    "searching": true,
                    "aaSorting": [],
                    "sScrollX": '100%',
                    "lengthMenu": [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
                    "aoColumnDefs": [{ 'bSortable': false, 'aTargets': [1] }],
   
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: str[0],
                        "paginate": {
                            "next": ">",
                            "previous": "<"
                        }
                    }
                });
            }.bind(this));
    
    
        },

      
    };
});

