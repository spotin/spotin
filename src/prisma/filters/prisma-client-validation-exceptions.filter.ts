import { Catch, ArgumentsHost, BadRequestException } from '@nestjs/common';
import { BaseExceptionFilter } from '@nestjs/core';
import { PrismaClientValidationError } from '@prisma/client/runtime/library';

@Catch(PrismaClientValidationError)
export class PrismaClientValidationExceptionsFilter extends BaseExceptionFilter {
  catch(exception: PrismaClientValidationError, host: ArgumentsHost): void {
    super.catch(new BadRequestException(), host);
  }
}
