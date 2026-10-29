<x-app-layout>
  <x-slot name="header">
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


      <title>Document</title>
    </head>
    <style>
      .add {
        margin-left: 600px;
      }


      /* .form-control {
        width: auto;
      } */

      /* .user_details {
        margin-left: 10px;
      } */

      /* .userget {
      margin-left: -203px;
    } */
    </style>

    <body>

      <h3 class="add"> Add User</h3>
      <br>
      <form>
        <div class="form-group row user_details">
          <label for="inputEmail3" class="col-sm-1 col-form-label">Role*:</label>
          <div class="form-group col-md-4">
            <select id="inputState" class="">
              <option selected> Selecet Role...</option>
              <option>...</option>
            </select>
          </div>
        </div>
        <br>
        <h2 class="user" style="margin-left:15px">User Detail*:</h2>
        <div class="conteiner">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-4 col-form-label">Carrier Company Name</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="inputPassword3" placeholder="search">

                </div>
                <br>
                <br>

                <div class="form-group row">
                  <label for="inputPassword3" class="col-sm-4 col-form-label">Carrier Owner Id</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="inputPassword3" placeholder="id">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-4 col-form-label">Carrier Owner Name</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="inputPassword3" placeholder="name">
                </div>
              </div>
            </div>
          </div>
        </div>
        <br>

        <div class="conteiner">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group row user_details">
                <label for="inputEmail3" class="col-sm-4 col-form-label">Status*:</label>
                <div class="form-group col-md-4">
                  <select id="inputState" class="form-control">
                    <option selected> Active</option>
                    <option>...</option>
                  </select>
                </div>
              </div>
              <br>
              <div class="form-group row user_details">
                <label for="inputPassword3" class="col-sm-4 col-form-label">Email:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="inputPassword3" placeholder="email">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-4 col-form-label">Primary Contact Number:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="inputPassword3" placeholder="phone number">
                </div>
              </div>
            </div>
          </div>
        </div>
        <br>
        <div class="conteiner">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group row user_details">
                <label for="inputEmail3" class="col-sm-4 col-form-label">truck Unit*:</label>
                <div class="form-group col-md-4">
                  <select id="inputState" class="form-control">
                    <option selected> SOD TRUCK U012</option>
                    <option>...</option>
                  </select>
                </div>
              </div>
              <br>
              <div class="form-group row user_details">
                <label for="inputPassword3" class="col-sm-4 col-form-label">VIN:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="inputPassword3" placeholder="">
                </div>
              </div>
              <br>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">
                  Mkae It default
                </label>

              </div>
            </div>
            <div class="col-md-6">

              <div class="form-check form-switch">
                <label class="form-check-label" for="flexSwitchCheckChecked">Resequencing of shipments
                  within Route allowed:</label>

                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>

              </div>
            </div>
          </div>
        </div>
        <br>
        <div class="conatiner">
          <div class="row user_details">
            <div class="col-md-6">
              <button type="button" class="btn btn-success add-sub-driver add_button">Add Sub-driver</button>


            </div>
          </div>
        </div>

      </form>


      <div class="field_wrapper">
        <style>
          /* Initially hide all forms */

          .field_wrapper {
            display: none;

          }

          .add {
            margin-left: 600px;
          }
        </style>



    </body>

    </html>
  </x-slot>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('.add-sub-driver').on('click', function(e) {

      $('.field_wrapper').show();
    });
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper');
    var fieldHTML = `<div class = "section">
  <div class="container">
  <h3 class="add"> Add Sub-driver</h3>
        <br>
        <form id="" class="sub-form">
          <br>
          <h2 class="user" style="margin-left:15px">User Detail*:</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group row user_details">
          <label for="inputEmail3" class="col-sm-4 col-form-label">Driver ID</label>
          <div class="col-sm-7">
            <input type="text" class="" id="inputPassword3" placeholder="D0001" name="D0001[]">
          </div>
        </div>
        <br>

        <div class="form-group row">
          <label for="inputPassword3" class="col-sm-4 col-form-label">Name:</label>
          <div class="col-sm-7">
            <input type="text" class="form-control" name="name[]" id="inputPassword3" placeholder="Name">
          </div>
        </div>
        <br>

        <div class="form-group row user_details">
          <label for="inputPassword3" class="col-sm-4 col-form-label">Email ID:</label>
          <div class="col-sm-7">
            <input type="text" class="form-control" id="inputPassword3" name="email[]" placeholder="Email">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group row user_details">
          <label for="inputEmail3" class="col-sm-4 col-form-label">Status*:</label>
          <div class="form-group col-md-4">
            <select id="inputState" class="form-control" name="status[]">
              <option selected> Active</option>
              <option>...</option>
            </select>
          </div>
        </div>
        <br>
        <div class="form-group row">
          <label for="inputPassword3" class="col-sm-4 col-form-label">Primary Contact Number:</label>
          <div class="col-sm-7">
            <input type="text" class="form-control" id="inputPassword3" placeholder="Phone Number" name="phone_number[]">
          </div>
        </div>
        <br>

        <div class="form-group row user_details">
          <label for="inputEmail3" class="col-sm-4 col-form-label">Role*:</label>
          <div class="form-group col-md-4">
            <select id="inputState" name="role[]" class="form-control">
              <option selected> Select Role...</option>
              <option>...</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br> <div class="conteiner">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group row user_details">
                  <label for="inputEmail3" class="col-sm-4 col-form-label">truck Unit*:</label>
                  <div class="form-group col-md-4">
                    <select id="inputState" name="truck[]" class="form-control">
                      <option selected> SOD TRUCK U012</option>
                      <option>...</option>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-group row user_details">
                  <label for="inputPassword3" class="col-sm-4 col-form-label">VIN:</label>
                  <div class="col-sm-7">
                    <input type="text" name="vin[]" class="form-control" id="inputPassword3" placeholder="">
                  </div>
                </div>
                <br>

                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Mkae It default
                  </label>

                </div>
              </div>
              <div class="col-md-6">

                <div class="form-check form-switch">
                  <label class="form-check-label" for="flexSwitchCheckChecked">Resequencing of shipments
                    within Route allowed:</label>

                  <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>

                </div>
              </div>
            </div>
          </div>
      <div class="container">
        <div class="row" style="margin-left:700px">
          <div class="col-sm-2">

            <button type="reset" class="btn btn-danger">cancel</button>
          </div>
          <div class="col-sm-2">

            <button type="submit" class="btn btn-success">save</button>
          </div>
        </div>
      </div>
          <br><button type="button" class=" btn btn-danger remove_button">remove Sub-driver</button></div> </form>
      </div>`;


    var x = 1; //Initial field counter is 1

    // Once add button is clicked correct this cosde and 
    $(addButton).click(function() {
      //Check maximum number of input fields
      if (x < maxField) {
        x++; //Increase field counter
        $(wrapper).append(fieldHTML); //Add field html
      } else {
        alert('A maximum of ' + maxField + ' fields are allowed to be added. ');
      }
    });

    // Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e) {
      e.preventDefault();
      $(this).parent('div').remove(); //Remove field html
      x--; //Decrease field counter
    });
  });
</script>