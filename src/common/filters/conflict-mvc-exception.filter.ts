import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpStatus,
} from '@nestjs/common';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';
import { Request, Response } from 'express';

@Catch(PrismaClientKnownRequestError)
export class ConflictMvcExceptionFilter implements ExceptionFilter {
  catch(exception: PrismaClientKnownRequestError, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const request = ctx.getRequest<Request>();
    const response = ctx.getResponse<Response>();

    if (exception.code !== 'P2002') {
      throw exception;
    }

    const target = exception.meta?.target as string | undefined;

    if (target) {
      const errors: Record<string, string[]> = {
        [target]: [`An entity already exists with this ${target}.`],
      };

      request.session.errors = errors;
    }

    request.session.body = request.body;

    const origin = request.get('Referrer') as string;

    response.redirect(HttpStatus.FOUND, origin);
  }
}
