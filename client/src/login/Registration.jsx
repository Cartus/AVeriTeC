import React from 'react';
import Avatar from '@material-ui/core/Avatar';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Link from '@material-ui/core/Link';
import Grid from '@material-ui/core/Grid';
import AssignmentIcon from '@material-ui/icons/Assignment';
import Typography from '@material-ui/core/Typography';
import Container from '@material-ui/core/Container';
import styled from 'styled-components';
import axios from 'axios';

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

let md5 = require('md5');

class Registration extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      name: '',
      password: '',
      registered: false
    }
  }

  handleFormSubmit = e => {
    e.preventDefault();
    console.log(this.state)
    axios({
      method: 'post',
      url: "http://localhost:8081/api/registration.php",
      // url: "http://api.averitec.eu/registration.php",
      headers: {'content-type': 'application/json'},
      data: {
        name: this.state.name,
        password: this.state.password,
        password_md5: md5(this.state.password)
      }
    })
        .then(result => {
          console.log(result.data);
          this.setState({
            registered: result.data.registered
          })
        })
        // .then(res => console.log(res.data))
        .catch(error => this.setState({error: error.message}));
  };

  render() {
    return (
        <Container>
          <div>
            <TopSpacing/>
            <AvatarBox>
              <Avatar>
                <AssignmentIcon />
              </Avatar>
            </AvatarBox>
            <AvatarBox>
              <Typography component="h1" variant="h5">Register</Typography>
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
              {/*<TextField*/}
              {/*    variant="outlined"*/}
              {/*    margin="normal"*/}
              {/*    required*/}
              {/*    fullWidth*/}
              {/*    name="repeat_password"*/}
              {/*    label="Repeat Password"*/}
              {/*    type="password"*/}
              {/*    id="repeat_password"*/}
              {/*    onChange={e => this.setState({repeat_password: e.target.value })}*/}
              {/*/>*/}
              <Button
                  type="submit"
                  fullWidth
                  variant="contained"
                  color="primary"
                  onClick={e => this.handleFormSubmit(e)}
              >
                Register
              </Button>
              <Grid container>
                <Grid item xs>
                </Grid>
                <Grid item>
                  <Link href="/login" variant="body2">
                    {"Already have an account? Log in"}
                  </Link>
                </Grid>
              </Grid>
              <div>
                {this.state.registered &&
                <div>Thank you for registration.</div>}
              </div>
            </form>
          </div>
        </Container>
    );
  }
}

export default Registration;
