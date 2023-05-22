import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpException,
  UnauthorizedException,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';

@Catch(UnauthorizedException)
export class UnauthorizedViewsExceptionFilter implements ExceptionFilter {
  catch(_: HttpException, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();

    response.redirect(HttpStatus.FOUND, '/auth/login');
  }
}
