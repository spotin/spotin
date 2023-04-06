import { Catch, ArgumentsHost, BadRequestException } from '@nestjs/common';
import { BaseExceptionFilter } from '@nestjs/core';
import { PrismaClientUnknownRequestError } from '@prisma/client/runtime/library';

@Catch(PrismaClientUnknownRequestError)
export class PrismaClientUnknownRequestExceptionsFilter extends BaseExceptionFilter {
  catch(exception: PrismaClientUnknownRequestError, host: ArgumentsHost): void {
    const { message } = exception;

    if (new RegExp('Invalid value for .* argument').test(message)) {
      super.catch(new BadRequestException(), host);
    } else {
      super.catch(exception, host);
    }
  }
}
