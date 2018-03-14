/**
 * Created by Ed on 02/01/2017.
 */
module.exports = class {

    name;
    skill; 
    password; 
    confirm_password;
    email; 
    profile_pic;
    role;

    constructor(userInfoAll) {

        this.name = userInfoAll.name ? userInfoAll.name.trim() : '';
        this.skill = userInfoAll.skill ? userInfoAll.skill.trim() : '';
        this.password = this.confirm_password  = "";
        this.email = userInfoAll.email ? userInfoAll.email.trim() : '';
        this.profile_pic = userInfoAll.profile_pic ? userInfoAll.profile_pic: 'images/avatar.jpg';
        this.role = userInfoAll.role ? userInfoAll.role : '';
        
    }
};