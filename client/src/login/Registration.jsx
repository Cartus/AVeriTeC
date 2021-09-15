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

export default function Registration() {
  
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
            />
            <TextField
              variant="outlined"
              margin="normal"
              required
              fullWidth
              name="repeat_password"
              label="Repeat Password"
              type="password"
              id="repeat_password"
            />
            <Button
              type="submit"
              fullWidth
              variant="contained"
              color="primary"
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
          </form>
        </div>
      </Container>
    );
  }