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

export default function Login() {
  
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
              id="email"
              label="Email Address"
              name="email"
              autoComplete="email"
              autoFocus
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
              autoComplete="current-password"
            />
            <Button
              type="submit"
              fullWidth
              variant="contained"
              color="primary"
            >
              Sign In
            </Button>
            <Grid container>
              <Grid item xs>
                <Link href="#" variant="body2">
                  Forgot password?
                </Link>
              </Grid>
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