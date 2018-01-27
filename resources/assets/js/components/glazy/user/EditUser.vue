<template>
<div class="row edit-user">
    <div class="col-md-12">
        <b-alert v-if="apiError" show variant="danger">
            API Error: {{ apiError.message }}
        </b-alert>
        <b-alert v-if="serverError" show variant="danger">
            Server Error: {{ serverError }}
        </b-alert>
        <form  role="form" method="POST" v-if="isLoaded">
            <div>
                <h3 class="card-title">
                    Edit Your Account
                </h3>
            </div>

            <b-form-group
                    id="groupName"
                    description="Your full name or handle"
                    label="Your Name"
                    :feedback="feedbackName"
                    :state="stateName"
            >
                <b-form-input id="name" :state="stateName" v-model.trim="form.name"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupCurrentPassword"
                    label="Current Password"
            >
                <b-form-input id="currentPassword" type="password" v-model.trim="form.currentPassword"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupNewPassword"
                    label="New Password"
            >
                <b-form-input id="newPassword" type="password" v-model.trim="form.newPassword"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupNewPasswordAgain"
                    label="New Password (Again)"
            >
                <b-form-input id="newPasswordAgain" type="password" v-model.trim="form.newPasswordAgain"></b-form-input>
            </b-form-group>

            <b-form-group id="groupButtons">
                <b-button class="float-right"
                          size="sm"
                          variant="primary"
                          @click.prevent="update"><i class="fa fa-save"></i> Update</b-button>
                <b-button class="float-right"
                          size="sm"
                          variant="secondary"
                          @click.prevent="cancelEdit()"><i class="fa fa-times"></i> Cancel</b-button>
            </b-form-group>
        </form>

    </div>
</div>

</template>


<script>
  export default {
    props: {
      recipe: {
        type: Object,
        default: null
      }
    },
    components: {
    },
    data() {
      return {
        user: null,
        form: {
          name: '',
          currentPassword: '',
          newPassword: '',
          newPasswordAgain: ''
        },
        errors: [],
        apiError: null,
        serverError: null
      }
    },
    created() {
      if (this.$auth.check()) {
        this.user = this.$auth.user()
        this.form = {
          _method: 'PATCH',
          id: this.user.id,
          name: this.user.name,
        }
      }
    },
    computed: {
      isLoaded: function () {
        if (this.$auth.check()) {
          return true;
        }
        return false;
      },
      feedbackName() {
        return this.form.name.length > 0 ? 'Enter at least 3 characters' : 'Please enter your name';
      },
      stateName() {
        return this.form.name.length > 2 ? 'valid' : 'invalid';
      },
    },
    methods: {
      update: function () {
        if (this.isLoaded) {
          this.$emit('isProcessing');

          Vue.axios.post(Vue.axios.defaults.baseURL + '/user/' + this.user.id, this.form)
            .then((response) => {
              console.log('got response:')
              console.log(response)
              if (response.data.error) {
                // error
                this.apiError = response.data.error
                console.log(this.apiError)
              } else {
                console.log('emit updatedUser')
                this.$emit('updatedUser')
              }
            })
            .catch(response => {
              this.serverError = response;
              console.log('UPDATE USER ERROR')
              console.log(response.data)
            })
        }
      },

      cancelEdit: function () {
        this.$emit('editUserCancel');
      }

    }
  }
</script>

<style>
    .edit-user label {
        margin-bottom: 0;
    }
</style>