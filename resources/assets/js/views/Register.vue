<template>

    <div class="row content-row">
        <div class="col-md-4 offset-md-4 col-sm-12">
            <b-card title="Join Glazy">
                <b-alert v-if="serverError" show variant="danger">
                    {{ serverError }}
                </b-alert>

                <div>
                    <b-form-group
                            id="nameGroup"
                            label-for="name"
                            label="Your Name">
                        <b-form-input v-model.trim="form.name"
                                      type="text"
                                      id="name"
                                      :state="nameState"
                                      aria-describedby="input-help input-feeback"
                                      placeholder="Jane Doe"></b-form-input>
                        <b-form-feedback id="name-feedback">
                            Enter at least three letters.
                        </b-form-feedback>
                    </b-form-group>
                    <b-form-group
                            id="emailGroup"
                            label-for="email"
                            label="Email Address">
                        <b-form-input v-model.trim="form.email"
                                      type="email"
                                      id="email"
                                      :state="emailState"
                                      aria-describedby="input-help input-feeback"
                                      placeholder="jane@doe.com"></b-form-input>
                        <b-form-feedback id="email-feedback">
                            Invalid email address
                        </b-form-feedback>
                    </b-form-group>
                    <b-form-group
                            id="passwordGroup"
                            label-for="password"
                            label="Password">
                        <b-form-input
                                id="password"
                                v-model.trim="form.password"
                                :state="passwordState"
                                aria-describedby="input-help input-feeback"
                                type="password"></b-form-input>
                        <b-form-feedback id="password-feedback">
                            Enter at least six characters.
                        </b-form-feedback>
                    </b-form-group>
                    <b-form-group
                            id="allowContactGroup"
                            label-for="accept-terms"
                            label="Accept <a href='http://help.glazy.org/about/terms-of-service.html'>Terms of Service</a> and <a href='http://help.glazy.org/about/privacy.html'>Privacy Policy</a> (Required.)">
                        <b-form-checkbox plain 
                                          id="accept-terms"
                                          v-model="form.acceptTerms"
                                          value="accepted"
                                          unchecked-value="not_accepted">
                                        I accept
                        </b-form-checkbox>
                    </b-form-group>
                    <div>
                        <b-btn size="sm" class="float-left" variant="secondary" @click="cancelRegister()">
                            Cancel
                        </b-btn>
                        <b-btn v-if="form.acceptTerms" size="sm" class="float-right" variant="info" @click="register()">
                            Join Now!
                        </b-btn>
                    </div>
                </div>
            </b-card>
        </div>
    </div>

</template>

<script>

  export default {
    name: 'Register',
    props: {
    },
    data() {
      return {
        context: 'register context',
        form: {
          name: '',
          email: '',
          password: '',
          acceptTerms: false
        },
        serverError: null,
        errors: null
      }
    },
    computed : {
      nameState: function () {
        return this.form.name.length > 2 ? 'valid' : 'invalid'
      },
      emailState: function () {
        var re = /\S+@\S+\.\S+/
        if (re.test(this.form.email)) {
          return 'valid'
        }
        return 'invalid'
      },
      passwordState: function () {
        return this.form.password.length > 5 ? 'valid' : 'invalid'
      },
      aggregateState: function () {
        if (this.nameState === 'valid' &&
          this.emailState === 'valid' &&
          this.passwordState === 'valid') {
          return true
        }
        return false
      }
    },
    methods: {
      register () {
        this.serverError = null

        if (!this.aggregateState) {
          console.log('not passing')
          return
        }
        console.log('going to register')
        this.$auth.register({
          data: this.form,
          redirect: null, // overriding the redirect-to-login screen
          autoLogin: true, // doesn't work
          success (res) {
              /* Autologin doesn't seem to work, so I'm passing the login request manually using the same form data */
            this.$auth.login({data: this.form})
            console.log('success ' + this.context)
          },
          error (res) {

            this.serverError = 'Error Registering.'

            console.log('error ' + this.context)
            if (res.response.data && res.response.data.error) {
              this.errors = res.response.data.error.errors
            }
            if (res.response.status &&
              Number(res.response.status) === 500 ) {
            }
            if (res.response.status &&
              Number(res.response.status) === 403 ) {
              this.serverError = 'This email is already in use.'
            }
          }
        })
      },
      cancelRegister() {
        this.$router.push('search')
      }

    }
  }

</script>

<style>
    .content-row {
        padding-top: 15px;
    }
</style>