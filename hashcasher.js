if (window.addEventListener) {
  window.addEventListener('load', loader, true);
} else {
  window.attachEvent('load', loader);
}

function loader (e) {
  var targets = document.querySelectorAll('form[require=hashcash]');
  console.log('[HASHCASH]', 'found', targets.length, 'targets:', targets);
  
  for (var i = 0; i < targets.length; i++) {
    var element = targets[i];
    if (element.addEventListener) {
      element.addEventListener('submit', privateEventHandler, true);
    } else {
      element.attachEvent('onsubmit', privateEventHandler);
    }
  }
}

function resetRegistrationControls () {
  let element = document.querySelector('form[require=hashcash]');
  element.classList.remove('loading');

  $('button[type=submit]').show();
  $('.loading').hide();
}

function privateEventHandler(e) {
  e.preventDefault();
  var element = this;
  var $element = $(this);
  var errors = element.querySelector('.errors');
  var errorList = element.querySelector('.errors > ul');

  element.classList.add('loading');

  $('button[type=submit]').hide();
  $('.loading').show();

  var worker = new Worker('worker.js');
  worker.onmessage = function hashcashWorkerHandler (e) {
    var cash = e.data.cash;
    var bits = e.data.bits;
    var input = e.data.input;
    var data = e.data.data;
    var xhr = new XMLHttpRequest();

    xhr.open('POST', element.target, true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Hashcash', cash);
    xhr.setRequestHeader('X-Hashcash-Bits', bits);
    xhr.setRequestHeader('X-Hashcash-Input', input);
    xhr.onreadystatechange = function hashcashStateChange () {
      if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
        // Request finished. Do processing here.
        try {
          var response = JSON.parse(xhr.responseText);
        } catch (e) {
          var response = xhr.responseText;
        }

        console.log('got response:', typeof response, response);

        while (errorList.hasChildNodes()) {
          errorList.removeChild(errorList.children[0]);
        }

        // TODO: investigate registration cases
        if (response && response.status) {
          console.log('response has status:', response.status);

          if (response.status === 'error') {
            console.log('status is error:', response);

            response.errors.forEach(function(message) {
              var error = document.createElement('li');
              error.innerText = message;
              errorList.appendChild(error);

              element.classList.add('error');
              element.classList.remove('loading');

              $('button[type=submit]').show();
              $('.loading').hide();
            });
          }

          if (response.status === 'success') {
            console.log('status is success:', response);

            gtag_report_conversion();

            response.event_category = 'growth';
            response.event_callback = function () {
              console.log('redirect to:', response.result.next);
              window.location = response.result.next || '/';
            };

            //gtag('event', 'registration', response);

            setTimeout(function () {
              console.log('redirect to:', response.result.next);
              window.location = response.result.next || '/';
            }, 1000);

            if (dataLayer) {
              dataLayer.push({
                'event': {
                  'action': 'registration',
                  'category': 'growth'
                },
                'eventCallback': function () {
                  console.log('redirect to:', response.result.next);
                  window.location = response.result.next || '/';
                }
              });
            }

            /*_gaq.push(['_set','hitCallback',function() {
              window.location = '/';
            }]);
            _gaq.push(['_trackEvent', 'growth', 'registration']);*/
          }
        }
      }
    }

    xhr.send(data);
    setTimeout(resetRegistrationControls, 1000);
  }

  var fields = {};
  var data = this.querySelectorAll('input, textarea, select');

  for (var i = 0; i < data.length - 1; i++) {
    if (~([
      'text',
      'email',
      'hidden',
      'password'
    ].indexOf(data[i].type))) {
      fields[data[i].name.toString()] = data[i].value.trim();
    }
  }

  // TODO: store difficulty from response headers, maintain memory
  var difficulty = 2;
  var fieldsEncoded =  Object.keys(fields).map(function(key) {
    return key + '=' + fields[key];
  }).join('&');

  worker.postMessage([ fieldsEncoded , difficulty ]);

  return false;
}
