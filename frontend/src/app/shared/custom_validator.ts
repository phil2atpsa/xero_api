
export  class CustomValidator {

  static emailValidator(email): any {
    if (email.pristine) {
      return null;
    }
    const EMAIL_REGEXP = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    email.markAsTouched();
    if (EMAIL_REGEXP.test(email.value)) {
      return null;
    }
    return {
      invalidEmail: true
    };
  }

}
