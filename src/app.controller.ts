import { Get, Controller, Request, Render, UseGuards } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';
import { AppService } from './app.service';
import { JwtAuthGuard } from './auth/jwt-auth-guards';

@ApiTags('views')
@Controller()
export class AppController {
  constructor(private readonly appService: AppService) {}

  @Get()
  @Render('index')
  root() {
    const hello = this.appService.getHello();

    return { hello: hello };
  }

  @UseGuards(JwtAuthGuard)
  @Get('profile')
  getProfile(@Request() req: any) {
    return req.user;
  }
}
