var product_id = "b229622b"; // LiveAgent
var variationId = "3513230f"; // Trial (not billable)
var language_code = 'en-US';
var apikey = "jx5imiBhB6K12zui03YJL0lumHOr7S5T";
var source = 'magento';
var requestData = null;
var formKey = '';

(function () { 
function sendApiRequest(options) {
  options.dataType = "json";

  if(options.data) {
    options.data = JSON.stringify(options.data);
  }

  if(options.params) {
    options.url += "?" + jQuery.param(options.params);
    options.params = undefined;
  }

  jQuery.ajax(options);
}

var Api = function () {
  this.url = "https://crm.qualityunit.com/api/v3/";
  this.method = "GET";
};

//retrieves list of countries
Api.countriesGet = function () {
  Api.apply(this, arguments);
  this.url += "countries";
};

//retrieves price
Api.priceGet = function () {
  Api.apply(this, arguments);
  this.url += "billing/price";
  this.params = {
    //variationId:
    //country:
    //vatId: optional
    //addons: optional array
  };
};

Api.addonsGet = function (variationId) {
  Api.apply(this, arguments);
  this.url += "variations/" + variationId + "/available_addons";
};

//checks vat validity
Api.vatCheck = function () {
  Api.apply(this, arguments);
  this.url += "billing/_check_vat";
  this.method = "POST";
  this.params = {
    //vatId:
  }
};

//checks domain validity
Api.domainCheck = function () {
  Api.apply(this, arguments);
  this.url += "subscriptions/_check_domain";
  this.method = "POST";
  this.params = {
    //product_id:
    //subdomain:
  }
};

//checks install progress
Api.installProgress = function (subscription_id) {
  Api.apply(this, arguments);
  this.url += "subscriptions/" + subscription_id + "/install_progress";
};

//new subscription
Api.signup = function () {
  Api.apply(this, arguments);
  this.url += "subscriptions/";
  this.method = "POST";
  this.data = {
    //variation_id:
    //subdomain:
    //language:
    //initial_api_key:
    //billing_period:
    //note:
    //pap_visitor_id:
    //customer: {
    //  name:
    //  email:
    //  company:
    //  phone:
    //  address1:
    //  address2:
    //  city:
    //  state:
    //  country:
    //  zip:
    //  vat_id:
    //  tax_id:
    //},
    //payment_method: {
    //  payment_type:
    //  payment_token:
    //  card_name:
    //  card_holder:
    //  card_address:
    //  card_zip:
    //  card_expire:
    //}
  };
};
// SignupForm file
function _generateAccessor(fieldName, accessor) {
  return (function (reset) {
    reset = typeof reset !== "undefined" ? reset : false;
    if (reset || !this[fieldName]) {
      this[fieldName] = accessor.call(this, reset);
    }
    return this[fieldName];
  })
}

function setVisible(element, value) {
  if (value) {
    element.removeClass("invisible");
  } else {
    element.addClass("invisible");
  }
}

var queryString = (function (a) {
  if (a == "") {
    return {};
  }
  var b = {};
  for (var i = 0; i < a.length; ++i) {
    var p = a[i].split('=', 2);
    if (p.length == 1) {
      b[p[0]] = "";
    } else {
      b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
  }
  return b;
})(window.location.search.substr(1).split('&'));

////////// FormField
function FormField(name) {
  this._name = name;
  this.active = true;
  this.validators = [];
  this.validationTimer = undefined;
  this.paymentMethod = undefined;
}

FormField.prototype = {
  constructor: FormField,

  block: _generateAccessor("_block", function (reset) {
    return jQuery("." + this._name);
  }),

  main: _generateAccessor("_main", function (reset) {
    return jQuery("#" + this._name + "main");
  }),

  input: _generateAccessor("_input", function (reset) {
    return this.main(reset).find("input");
  }),

  select: _generateAccessor("_select", function (reset) {
    return this.main(reset).find("select");
  }),

  value: function (reset) {
    reset = typeof reset !== "undefined" ? reset : false;
    return this.input(reset).val();
  },

  setActive: function (value) {
    this.active = value === true;
    if (!this.active) {
      this.setState("", "");
    } else if (this.value()) {
      this.validate();
    }
    this.onActivityChange(value);
  },

  onActivityChange: function (value) {

  },

  isActive: function () {
    return this.active;
  },

  setState: function (state, message) {
    message = typeof message !== "undefined" ? message : "";
    var field = this.main();
    for (var s in FormField.states) {
      if (FormField.states[s] === state) {
        field.addClass(FormField.states[s]);
      } else {
        field.removeClass(FormField.states[s]);
      }
    }
    this.errorMessage(message)
  },

  errorMessage: function (message) {
    this.main().find(".g-FormField2-Message").html(message)
  },

  validate: function () {
    if (this.validationTimer) {
      this.cancelValidation();
    }
    if (this.validators.length < 1 || !this.isActive()) {
      return;
    }

    var inst = this;
    var notifyResult = function (validator) {
      var waiting = false;
      for (var i = 0; i < inst.validators.length; i++) {
        var state = inst.validators[i].state;
        if (state === FormField.states.error) {
          inst.setState(FormField.states.error, inst.validators[i].message);
          return;
        }
        if (state === FormField.states.waiting) {
          waiting = true;
        }
      }
      if (waiting) {
        inst.setState(FormField.states.waiting, "Validating...");
        return;
      }
      inst.setState(FormField.states.valid, validator.message);
    };

    for (var i = 0; i < this.validators.length; i++) {
      this.validators[i].validate(this, notifyResult);
    }
  },

  getState: function () {
    var waiting = false;
    for (var i = 0; i < this.validators.length; i++) {
      var state = this.validators[i].state;
      if (state === FormField.states.error) {
        return FormField.states.error;
      }
      if (state === FormField.states.waiting) {
        waiting = true;
      }
    }
    if (waiting) {
      return FormField.states.waiting;
    }
    return FormField.states.valid;
  },

  scheduleValidation: function (time) {
    time = typeof time !== "undefined" ? time : 0;
    if (time <= 0) {
      this.validate();
      return;
    }
    var inst = this;
    if (this.validationTimer) {
      clearTimeout(this.validationTimer);
    }
    this.validationTimer = setTimeout(function () {
      inst.validationTimer = undefined;
      inst.validate();
    }, time);
  },

  cancelValidation: function () {
    if (this.validationTimer) {
      clearTimeout(this.validationTimer);
      this.validationTimer = undefined;
    }
  },

  registerValidator: function (validator) {
    if (jQuery.inArray(validator, this.validators) >= 0) {
      return;
    }
    this.validators.push(validator);
    return this;
  },

  validateOn: function (event, time, selector) {
    time = typeof time !== "undefined" ? time : 0;
    var inst = this;
    var runValidate = function () {
      inst.scheduleValidation(time);
    };
    if (typeof selector !== "undefined") {
      this.main().find(selector).on(event, runValidate);
    } else {
      this.main().on(event, runValidate);
    }
    return this;
  }
};

FormField.states = {
  error: "g-FormField2-Error",
  waiting: "g-FormField2-Waiting",
  valid: "g-FormField2-Valid"
};

////////// Field Vaidator

function FieldValidator() {
  this.state = FormField.states.error;
  this.message = "Field invalid";
  this.unique = [];
}

FieldValidator.prototype = {
  constructor: FieldValidator,

  valid: function (message) {
    this.state = FormField.states.valid;
    this.message = message;
  },

  error: function (message) {
    this.state = FormField.states.error;
    this.message = message;
  },

  waiting: function () {
    this.state = FormField.states.waiting;
    this.message = "";
  },

  setUnique: function () {
    this.unique = [];
    for (var i = 0; i < arguments.length; i++) {
      this.unique.push(arguments[i]);
    }
  },

  uniqueChanged: function () {
    if (this.unique.length != arguments.length) {
      return true;
    }
    for (var i = 0; i < arguments.length; i++) {
      if (this.unique[i] !== arguments[i]) {
        return true;
      }
    }
    return false;
  }
};

FieldValidator.textLength = function (errorMessage, minimum, selector) {
  errorMessage = typeof errorMessage !== "undefined"
      ? errorMessage : "Field can't be empty";
  selector = typeof selector !== "undefined" ? selector : "input";
  minimum = typeof minimum !== "undefined" ? minimum : 1;

  var validator = new FieldValidator();
  validator.message = errorMessage;

  validator.validate = function (formField, notifyResult) {
    var input = formField.main().find(selector).val();
    if (input && input.length >= minimum) {
      validator.valid();
    } else {
      validator.error(errorMessage);
    }
    notifyResult(this);
  };

  return validator;
};

FieldValidator.request = function (createOptions, precheckInput) {
  var validator = new FieldValidator();

  validator.precheck = precheckInput;
  if (typeof precheckInput === "undefined") {
    validator.precheck = function (formField) {
      var input = formField.input().val();

      if (input === "undefined" || input === "") {
        this.error("Field can't be empty");
        return true;
      }

      if (!this.uniqueChanged(input)) {
        return true;
      }
      this.setUnique(input);
      return false;
    };
  }

  validator.validate = function (formField, notifyResult) {
    if (this.precheck(formField)) {
      notifyResult(this);
      return;
    }
    var options = createOptions(formField);
    options.context = this;

    options.complete = function () {
      notifyResult(this);
    };

    sendApiRequest(options);

    this.waiting();
    notifyResult(this);
  };

  return validator;
};

FieldValidator.regex = function (regex, errorMessage, selector) {
  selector = typeof selector !== "undefined" ? selector : "input";

  var validator = new FieldValidator();
  validator.message = errorMessage;

  validator.validate = function (formField, notifyResult) {
    if (regex.test(formField.main().find(selector).val())) {
      validator.valid();
    } else {
      validator.error(errorMessage);
    }
    notifyResult(this);
  };

  return validator;
};

////////// SignupForm

function SignupForm() {

  this.formFields = {};

  this.submitButton = {

    isEnabled: function () {
      return !this.main().attr("disabled");
    },

    setEnabled: function (value) {
      if (value) {
        this.main().removeAttr("disabled");
      } else {
        this.main().attr("disabled", true);
      }
    },

    main: _generateAccessor("_main", function (reset) {
      return jQuery("#createButtonmain");
    }),

    text: _generateAccessor("_text", function (reset) {
      return jQuery("#createButtontextSpan");
    })
  };

  this.errorField = {
    display: function (message) {
      if (message) {
        this.main().html("<div>" + message + "</div>")
      } else {
        this.main().html("");
      }
    },

    main: _generateAccessor("_main", function (reset) {
      return jQuery("#signUpError");
    })
  }
}

SignupForm.prototype = {
  constructor: SignupForm,

  block: _generateAccessor("_block", function (reset) {
    return jQuery("#signup");
  }),

  getField: function (name) {
    if (!this.formFields[name]) {
      this.formFields[name] = new FormField(name);
    }
    return this.formFields[name];
  },

  setPaymentMethod: function (paymentMethod) {
    if (this.paymentMethod) {
      this.paymentMethod.disable();
    }
    if (paymentMethod) {
      paymentMethod.enable();
    }
    this.paymentMethod = paymentMethod;
  }
};

// Progress loader
function ProgressLoader() {
  this.dots = "";
}

ProgressLoader.prototype = {
  constructor: ProgressLoader(),

  block: _generateAccessor("_block", function (reset) {
    return jQuery('#loader');
  }),

  label: _generateAccessor("_label", function (reset) {
    return this.block(reset).find(".loader-label");
  }),

  percent: _generateAccessor("_percent", function (reset) {
    return this.block(reset).find(".percentage");
  }),

  bar: _generateAccessor("_bar", function (reset) {
    return this.block(reset).find(".progress-bar");
  }),

  setProgress: function (progress) {
    this.bar().width(progress + "%");
    this.percent().text(progress + "%");
    var label = this.label();
    if(this.dots.length > 2) {
      this.dots = ".";
    } else {
      this.dots += ".";
    }
    if (progress <= 33) {
      label.text("Installing" + this.dots);
    } else if (progress <= 66) {
      label.text("Launching" + this.dots);
    } else if (progress == 100){
      label.text("Redirecting" + this.dots);
    } else {
      label.text("Finalizing" + this.dots);
    }
  }
};

// progress loader init
var progressLoader = new ProgressLoader();
////// signup form init ///////
var sF = new SignupForm();

(function (f) {
  var identifier = "__generatedField__";
  f.nameField = identifier;
  f.mailField = identifier;
  f.domainField = identifier;

  for (var property in f) {
    if (f.hasOwnProperty(property)
        && f[property] === identifier) {
      f[property] = f.getField(property);
    }
  }
})(sF);
///////////////////////////////
// SignupLogic file

function parseError(response, def) {
    if (response.status == 500) {
        return def;
    }
    try {
        var errorData = JSON.parse(response.responseText);
        return errorData.message;
    } catch (ignore) {
    }
    return def;
}

function setEvents(formField) {
    formField.validateOn("focusout")
        .validateOn("keyup", 500)
        .validateOn("change", 500);
}

function initNameField() {
    var nameField = sF.nameField;
    nameField.registerValidator(FieldValidator.textLength());
    setEvents(nameField);
}

function initMailField() {
    //var mailRegex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    var mailRegex = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i;
    var regexMailValidator = FieldValidator.regex(mailRegex, "Email invalid");
    var mailField = sF.mailField;
    mailField.registerValidator(regexMailValidator);
    setEvents(mailField);
}

function initDomainField() {
    var crmOptionsCreator = function (formField) {
        var options = new Api.domainCheck();

        options.params = {
            productId: product_id,
            subdomain: formField.input().val()
        };

        options.success = function () {
            this.valid("Domain is valid");
        };

        options.error = function (jqxhr) {
            this.error(parseError(jqxhr, "Failed to validate domain"));
        };

        return options;
    };
    var crmDomainValidator = FieldValidator.request(crmOptionsCreator);

    var domainField = sF.domainField;
    domainField.registerValidator(crmDomainValidator);
    setEvents(domainField);

    domainField.input().alphanum({
        allow: "-0123456789",   // Allow extra characters
        disallow: "",   // Disallow extra characters
        allowSpace: false, // Allow the space character
        allowNumeric: true, // Allow digits 0-9
        allowUpper: true, // Allow upper case characters
        allowLower: true, // Allow lower case characters
        allowCaseless: false, // Allow characters that don't have both upper &
                              // lower variants - eg Arabic or Chinese
        allowLatin: true, // a-z A-Z
        allowOtherCharSets: false, // eg ? ? Arabic, Chinese etc
        forceLower: true
    });
}

function initFormFields() {
    initNameField();
    initMailField();
    initDomainField();
}

function doLoading(subscription) {
    //setVisible(progressLoader.block(), false);
    //setVisible(jQuery("#completed"), true);
    //

    var options = new Api.installProgress(subscription.id);

    options.success = function (data) {
        console.log(JSON.stringify(data));
        if (data.account_status == undefined || data.progress == undefined) {
            progressLoader.setProgress(0);
            progressLoader.label().text("Failed to retrieve valid progress info.");
            setTimeout(function () {
                doLoading(subscription);
            }, 700);
            return;
        }
        if (data.account_status == 'I') {
            progressLoader.setProgress(data.progress);
            setTimeout(function () {
                doLoading(subscription);
            }, 700);
        } else {
            progressLoader.setProgress(100);
            var redirectForm = '<form method="POST" action="' + jQuery('#continue').val() + 
            '"><input type="hidden" name="la-full-name" value="' + requestData.customer.name + 
			'"><input type="hidden" name="la-owner-email" value="' + requestData.customer.email + 
			'"><input type="hidden" name="la-url" value="' + requestData.subdomain + 
			'"><input type="hidden" name="apiKey" value="' + requestData.initial_api_key + 
			'"><input type="hidden" name="AuthToken" value="' + data.login_token +
			'"><input type="hidden" name="action" value="r"/><input type="hidden" name="form_key" value="' + formKey + '"/></form>';
            jQuery(redirectForm).appendTo('body').submit();
        }
    };

    options.error = function (jqxhr) {
        progressLoader.label().text(parseError(jqxhr, "Something went wrong when retrieving progress."));
    };

    sendApiRequest(options);
}

function completeSignup(subscription) {
    setVisible(sF.block(), false);
    setVisible(progressLoader.block(), true);
    ga('send', 'event', 'LA SignUp', jQuery('#plan').val());
    jQuery('<img height="1" width="1" src="//www.googleadservices.com/pagead/conversion/966671101/imp.gif?label=ER6zCKjv_1cQ_fX4zAM&amp;guid=ON&amp;script=0" />').appendTo('#signup');
    doLoading(subscription);
}

function sendSignupRequest(signupData) {
    var errorField = sF.errorField;
    var submitButton = sF.submitButton;
    
    var options = new Api.signup();
    options.data = signupData;
    requestData = signupData;
    formKey = jQuery('#form_key').val();

    options.success = function (subscription) {
        completeSignup(subscription);
    };

    options.error = function (jqxhr) {
        errorField.display(parseError(jqxhr, "Something went wrong."));
        submitButton.setEnabled(true);
        submitButton.text().html("Start now");
    };

    sendApiRequest(options);
}

function submitSignup(revalidate) {
    var formStates = FormField.states;
    var errorField = sF.errorField;
    var button = sF.submitButton;

    var error = false;
    var wait = false;

    for (var fieldName in sF.formFields) {
        var field = sF.formFields[fieldName];
        if (!field.isActive()) {
            console.info(fieldName + " inactive");
            continue;
        }
        console.info("Checking field: " + fieldName + "...");
        if (field.getState() === FormField.states.error) {
            if (!revalidate) {
                console.warn("Error");
                error = true;
                continue;
            }
            console.warn("Error: Validating...");
            field.validate();
            switch (field.getState()) {
                case formStates.waiting:
                    wait = true;
                    console.log("new status: Waiting...");
                    break;
                case formStates.error:
                    error = true;
                    console.warn("new status: Error");
                    break;
                default:
                    console.log("new status: Valid");
            }
            continue;
        }
        if (field.getState() === formStates.waiting) {
            console.log("Waiting...");
            wait = true;
        }
    }

    var defaultButtonText = "Start now";

    if (error) {
        console.warn("ERROR: form not submitted");
        console.log("\n---------------------------------\n\n");
        errorField.display("Some fields are invalid");
        button.text().html(defaultButtonText);
        button.setEnabled(true);
        return;
    }
    if (wait) {
        console.log("WAIT: form submit rescheduled");
        console.log("\n---------------------------------\n\n");
        errorField.display("");
        button.text().html("Validating...");
        setTimeout(function () {
            submitSignup(false);
        }, 500);
        return;
    }
    console.info("SUCCESS: form submitted");
    console.log("\n---------------------------------\n\n");

    errorField.display("");
    button.text().html("Creating...");
    sendSignupRequest({
        variation_id: variationId,
        subdomain: sF.domainField.value(),
        source_id: source,
        initial_api_key: randomString(),
        customer: {
            name: sF.nameField.value(),
            email: sF.mailField.value()
        }
    });
}

function initFormButton() {
    var button = sF.submitButton;

    button.main().click(function () {
        if (!button.isEnabled()) {
            return;
        }
        button.setEnabled(false);
        submitSignup(true);
    });
}

jQuery(function () {
    initFormFields();
    initFormButton();
    
    jQuery.fn.ghostInput = function(options) {
        var o = jQuery.extend({
            ghostText: ".domain.com",
            ghostPlaceholder: "Add subdomain",
            ghostTextClass: "domain"
        }, options);
        return this.each(function(i, element) {
            var $element = jQuery(element);
            if($element.ghostInputValidate)
                return true;
            $element.ghostInputValidate = true;
            var r = $element.attr("id") || "";
            o.ghostText = $element.attr("data-ghost-text") || o.ghostText;
            o.ghosttextspan = jQuery("<label />").text("");
            o.ghostHider = jQuery("<label />").css({"visibility":"hidden"});
            o.ghostBox = jQuery("<label />").attr("for", r).addClass(o.ghostTextClass).append(o.ghostHider).append(o.ghosttextspan);
            $element.parent().prepend(o.ghostBox);
            $element.bind("keyup keydown keypress change",function() {
                setTimeout(function() {
                    var t = "" == jQuery.trim($element.val()) ? "": o.ghostText;
                    o.ghostHider.text($element.val());
                    o.ghosttextspan.text(t)
                }, 0)
            });
            o.ghostBox.bind("click",function() {
                $element.focus();
            });
            return true;
        });
    };
    jQuery("#domainFieldinnerWidget").ghostInput();
});
})();