<template>

    <div class="row content-row">
        <div class="col-md-4 offset-md-4 col-sm-12">
            <b-card title="Join Glazy">
                <b-alert v-if="serverError" show variant="danger">
                    {{ serverError }}
                </b-alert>

                <div>
                    <b-form-group
                            id="name"
                            label="Your Name">
                        <b-form-input v-model.trim="form.name"
                                      type="text"
                                      :state="nameState"
                                      aria-describedby="input-help input-feeback"
                                      placeholder="Jane Doe"></b-form-input>
                        <b-form-feedback id="name-feedback">
                            Enter at least three letters.
                        </b-form-feedback>
                    </b-form-group>
                    <b-form-group
                            id="email"
                            label="Email Address">
                        <b-form-input v-model.trim="form.email"
                                      type="email"
                                      :state="emailState"
                                      aria-describedby="input-help input-feeback"
                                      placeholder="jane@doe.com"></b-form-input>
                        <b-form-feedback id="email-feedback">
                            Invalid email address
                        </b-form-feedback>
                    </b-form-group>
                    <b-form-group
                            id="password"
                            label="Password">
                        <b-form-input
                                id="login-form-password"
                                v-model.trim="form.password"
                                :state="passwordState"
                                aria-describedby="input-help input-feeback"
                                type="password"></b-form-input>
                        <b-form-feedback id="password-feedback">
                            Enter at least six characters.
                        </b-form-feedback>
                    </b-form-group>
                    <div>
                        <b-btn size="sm" class="float-left" variant="secondary" @click="cancelRegister()">
                            Cancel
                        </b-btn>
                        <b-btn size="sm" class="float-right" variant="info" @click="register()">
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
          password: ''
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
        if (!this.aggregateState) {
          console.log('not passing')
          return
        }
        console.log('going to register')
        this.$auth.register({
          data: this.form,
          // redirect: { name: 'search' },
          success (res) {
            console.log('success ' + this.context)
          },
          error (res) {
            console.log('error ' + this.context)
            if (res.response.data && res.response.data.error) {
              this.errors = res.response.data.error.errors
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