import React from 'react';
import Avatar from '@material-ui/core/Avatar';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Link from '@material-ui/core/Link';
import Grid from '@material-ui/core/Grid';
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import Typography from '@material-ui/core/Typography';
import Container from '@material-ui/core/Container';
import styled from 'styled-components';
import axios from 'axios';
import {Redirect} from "react-router-dom";
import md5 from "md5";
import config from "../config.json"

const AvatarBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 15px 15%;
  `

const TopSpacing = styled.div`
    height:15vh;
    width:100%;
`

class Login extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
      name: '',
      password: '',
      login: false
    }
  }

  handleFormSubmit = e => {
    e.preventDefault();
    var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/login.php",
          data:{
              name: this.state.name,
              password_md5: md5(this.state.password)
          }
      };

      console.log(this.state);
      axios(request).then((response) => {
          console.log(response.data);
          localStorage.setItem('user_id', response.data.user_id);
          localStorage.setItem('user_name', response.data.user_name);
          localStorage.setItem('login', response.data.login);
          localStorage.setItem('is_admin', response.data.is_admin);
          localStorage.finished_norm_annotations = response.data.finished_norm_annotations;
          localStorage.finished_qa_annotations = response.data.finished_qa_annotations;
          localStorage.finished_valid_annotations = response.data.finished_valid_annotations;
          localStorage.finished_p4_annotations = response.data.finished_p4_annotations? response.data.finished_p4_annotations : 0;
          localStorage.finished_p5_annotations = response.data.finished_p5_annotations? response.data.finished_p5_annotations : 0;
          localStorage.pc = 0;
          localStorage.claim_id = 0;
          localStorage.claim_norm_id = 0;
          this.setState({login: response.data.login})
          console.log(this.state);
          console.log(localStorage.getItem('login'));
      }).catch((error) => {window.alert(error)})	
  }

  render() {
    if (this.state.login) {
      return <Redirect to='/control'/>;
    }

    return (
        <Container>
          <div>
            <TopSpacing/>
            <AvatarBox>
              <Avatar>
                <LockOutlinedIcon />
              </Avatar>
            </AvatarBox>
            <AvatarBox>
              <Typography component="h1" variant="h5">Sign in</Typography>
            </AvatarBox>
            <form noValidate>
              <TextField
                  variant="outlined"
                  margin="normal"
                  required
                  fullWidth
                  id="name"
                  label="User Name"
                  name="name"
                  autoFocus
                  onChange={e => this.setState({name: e.target.value })}
              />
              <TextField
                  variant="outlined"
                  margin="normal"
                  required
                  fullWidth
                  name="password"
                  label="Password"
                  type="password"
                  id="password"
                  onChange={e => this.setState({password: e.target.value })}
              />
              <Button
                  type="submit"
                  fullWidth
                  variant="contained"
                  color="primary"
                  onClick={e => this.handleFormSubmit(e)}
              >
                Sign In
              </Button>
              <Grid container>
                {/*<Grid item xs>*/}
                {/*  <Link href="#" variant="body2">*/}
                {/*    Forgot password?*/}
                {/*  </Link>*/}
                {/*</Grid>*/}
                <Grid item>
                  <Link href="/register" variant="body2">
                    {"Don't have an account? Sign Up"}
                  </Link>
                </Grid>
              </Grid>
            </form>
          </div>
        </Container>
    );
  }
}

export default Login;
