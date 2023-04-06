import { Controller, Get, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller('auth')
export class AuthViewsController {
  @Get('login')
  @ApiOperation({
    summary: 'Render the login page',
    description: 'Render the login page.',
    operationId: 'loginView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/login')
  loginView() {
    return;
  }

  @Get('signup')
  @ApiOperation({
    summary: 'Render the sign up page',
    description: 'Render the sign up page.',
    operationId: 'signupView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/signup')
  signupView() {
    return;
  }
}
