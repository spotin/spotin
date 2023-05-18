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
export class ViewUnauthorizedExceptionFilter implements ExceptionFilter {
  catch(exception: HttpException, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const status = exception.getStatus();

    response
      .status(status)
      .redirect(HttpStatus.TEMPORARY_REDIRECT, '/auth/login');
  }
}
