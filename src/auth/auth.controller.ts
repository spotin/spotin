import {
  Controller,
  Request,
  Post,
  UseGuards,
  Res,
  UseFilters,
} from '@nestjs/common';
import { Response } from 'express';
import { ApiTags } from '@nestjs/swagger';
import { AuthService } from './auth.service';
import { LocalAuthGuard } from './local/local-auth.guard';
import { UnauthorizedExceptionFilter } from 'src/filters/unauthorized-exception.filter';

@ApiTags('Auth')
@Controller('api/auth')
export class AuthController {
  constructor(private authService: AuthService) {}

  @UseGuards(LocalAuthGuard)
  @UseFilters(new UnauthorizedExceptionFilter())
  @Post('login')
  async login(@Request() req: any, @Res() res: Response) {
    const { access_token } = await this.authService.login(req.user);
    return res
      .cookie('access_token', access_token, { httpOnly: true })
      .redirect('/spots/list');
  }
}
