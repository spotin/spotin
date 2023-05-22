import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpException,
  HttpStatus,
  BadRequestException,
} from '@nestjs/common';
import { Request, Response } from 'express';

@Catch(BadRequestException)
export class BadRequestViewsExceptionFilter implements ExceptionFilter {
  catch(exception: HttpException, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const request = ctx.getRequest<Request>();
    const response = ctx.getResponse<Response>();

    const exceptionResponse = exception.getResponse() as Record<string, any>;

    const errors: Record<string, string[]> = {};

    for (const error of exceptionResponse.message) {
      const key = error.split(' ')[0];
      const value = `${error.charAt(0).toUpperCase() + error.slice(1)}.`;

      if (!errors.hasOwnProperty(key)) {
        errors[key] = [];
      }

      errors[key].push(value);
    }

    request.session.errors = errors;

    const origin = request.get('Referrer') as string;

    response.redirect(HttpStatus.FOUND, origin);
  }
}
